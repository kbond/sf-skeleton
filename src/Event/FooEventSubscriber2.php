<?php

namespace App\Event;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FooEventSubscriber2 implements MessageSubscriberInterface
{
    public static function getHandledMessages(): array
    {
        return [
            FooEvent::class => 'doFoo',
        ];
    }

    public function doFoo(FooEvent $event)
    {
        //throw new \Exception();

        dump(__CLASS__);

        return __CLASS__;
    }
}
