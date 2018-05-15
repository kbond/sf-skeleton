<?php

namespace App\Messenger\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RetryMessageException extends \RuntimeException
{
    private $maxAttempts;
    private $delayInSeconds;

    public function __construct($message = '', \Throwable $previous = null, int $maxAttempts = 5, int $delayInSeconds = 0)
    {
        $this->maxAttempts = $maxAttempts;
        $this->delayInSeconds = $delayInSeconds;

        parent::__construct($message, 0, $previous);
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getDelayInSeconds(): int
    {
        return $this->delayInSeconds;
    }
}
