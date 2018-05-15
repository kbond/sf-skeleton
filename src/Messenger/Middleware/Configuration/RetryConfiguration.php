<?php

namespace App\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\EnvelopeItemInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RetryConfiguration implements EnvelopeItemInterface
{
    private $attempt;

    public function __construct(int $attempt)
    {
        $this->attempt = $attempt;
    }

    public function getAttempt(): int
    {
        return $this->attempt;
    }

    public function serialize()
    {
        return serialize([$this->attempt]);
    }

    public function unserialize($serialized)
    {
        [$this->attempt] = unserialize($serialized, ['allowed_classes' => false]);
    }
}
