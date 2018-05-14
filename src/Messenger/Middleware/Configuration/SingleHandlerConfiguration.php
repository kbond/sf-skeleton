<?php

namespace App\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\EnvelopeItemInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SingleHandlerConfiguration implements EnvelopeItemInterface
{
    private $handlerKey;

    public function __construct(string $handlerKey)
    {
        $this->handlerKey = $handlerKey;
    }

    public function getHandlerKey(): string
    {
        return $this->handlerKey;
    }

    public function serialize()
    {
        return serialize([$this->handlerKey]);
    }

    public function unserialize($serialized)
    {
        [$this->handlerKey] = unserialize($serialized, ['allowed_classes' => false]);
    }
}
