<?php

namespace App\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\EnvelopeItemInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RetryConfiguration implements EnvelopeItemInterface
{
    private $attempt;
    private $timeToRun;

    public function __construct(int $attempt, int $delayInSeconds)
    {
        $this->attempt = $attempt;
        $this->timeToRun = time() + $delayInSeconds;
    }

    public function getAttempt(): int
    {
        return $this->attempt;
    }

    public function getTimeToRun(): int
    {
        return $this->timeToRun;
    }

    public function serialize()
    {
        return serialize([
            $this->attempt,
            $this->timeToRun
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->attempt,
            $this->timeToRun
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }
}
