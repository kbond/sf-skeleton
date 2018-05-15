<?php

namespace App\Messenger\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RetryMessageException extends \RuntimeException
{
    private $maxAttempts;

    public function __construct($message = '', int $maxAttempts = 5, \Throwable $previous = null)
    {
        $this->maxAttempts = $maxAttempts;

        parent::__construct($message, 0, $previous);
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }
}
