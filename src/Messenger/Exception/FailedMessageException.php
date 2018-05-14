<?php

namespace App\Messenger\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FailedMessageException extends \RuntimeException
{
    private $violatingMessage;
    private $exception;
    private $handler;

    public function __construct(object $violatingMessage, \Throwable $exception, callable $handler)
    {
        $this->violatingMessage = $violatingMessage;
        $this->exception = $exception;
        $this->handler = $handler;

        parent::__construct();
    }

    public function getViolatingMessage(): object
    {
        return $this->violatingMessage;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getHandler(): callable
    {
        return $this->handler;
    }

    public function getHandlerServiceId(): string
    {
        if (\is_array($this->handler) && \is_object($this->handler[0])) {
            return \get_class($this->handler[0]).'::'.$this->handler[1];
        }

        if ($this->handler instanceof \Closure) {
            $ref = new \ReflectionFunction($this->handler);

            if (false !== strpos($ref->name, '{closure}')) {
                throw new \RuntimeException('Unable to parse service id from anonymous function.');
            }

            return $ref->getClosureScopeClass()->name.'::'.$ref->name;
        }

        if (\is_object($this->handler)) {
            return \get_class($this->handler);
        }

        throw new \RuntimeException('Unable to parse service id.');
    }
}
