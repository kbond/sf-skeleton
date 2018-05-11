<?php

namespace App\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FooEventSubscriber3 implements MessageHandlerInterface
{
    public function __invoke(FooEvent $event)
    {
        dump(__CLASS__);

        return __CLASS__;
    }
}
