<?php

namespace App\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FooEventSubscriber2 implements MessageHandlerInterface
{
    public function __invoke(FooEvent $event)
    {
        //throw new \Exception();

        dump(__CLASS__);

        return __CLASS__;
    }
}
