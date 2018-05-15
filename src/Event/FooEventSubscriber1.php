<?php

namespace App\Event;

use App\Messenger\Exception\RetryMessageException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FooEventSubscriber1 implements MessageHandlerInterface
{
    public function __invoke(FooEvent $event)
    {
        throw new RetryMessageException('need to retry this one too', null, 3, 1);
        //throw new \Exception();

        dump(__CLASS__);

        return __CLASS__;
    }
}
