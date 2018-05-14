<?php

namespace App\Messenger\Handler;

use App\Messenger\Exception\ChainFailedMessageException;
use App\Messenger\Exception\FailedMessageException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CatchExceptionChainHandler
{
    /**
     * @var callable[]
     */
    private $handlers;

    /**
     * @param callable[] $handlers
     */
    public function __construct(array $handlers)
    {
        if (empty($handlers)) {
            throw new \InvalidArgumentException('A collection of message handlers requires at least one handler.');
        }

        $this->handlers = $handlers;
    }

    public function __invoke($message)
    {
        $results = array();
        $errors = array();

        foreach ($this->handlers as $handler) {
            try {
                $results[] = $handler($message);
            } catch (\Throwable $e) {
                $errors[] = new FailedMessageException($message, $e, $handler);
            }
        }

        if (\count($errors)) {
            throw new ChainFailedMessageException(...$errors);
        }

        return $results;
    }
}
