<?php

namespace App\Messenger\Middleware;

use App\Messenger\Exception\ChainFailedMessageException;
use App\Messenger\Exception\FailedMessageException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\Locator\HandlerLocatorInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class WrapExceptionHandleMessageMiddleware implements MiddlewareInterface
{
    private $messageHandlerResolver;

    public function __construct(HandlerLocatorInterface $messageHandlerResolver)
    {
        $this->messageHandlerResolver = $messageHandlerResolver;
    }

    public function handle($message, callable $next)
    {
        $handler = $this->messageHandlerResolver->resolve($message);

        try {
            $result = $handler($message);
        } catch (NoHandlerForMessageException | ChainFailedMessageException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new FailedMessageException($message, $e, $handler);
        }

        $next($message);

        return $result;
    }
}
