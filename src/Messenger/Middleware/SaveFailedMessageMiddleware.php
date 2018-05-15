<?php

namespace App\Messenger\Middleware;

use App\Messenger\Exception\ChainFailedMessageException;
use App\Messenger\Exception\FailedMessageException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SaveFailedMessageMiddleware implements MiddlewareInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle($message, callable $next)
    {
        try {
            return $next($message);
        } catch (FailedMessageException $e) {
            $this->saveFailedMessage($e);
        } catch (ChainFailedMessageException $e) {
            foreach ($e->getFailedMessages() as $failedMessage) {
                $this->saveFailedMessage($failedMessage);
            }
        }

        return null;
    }

    private function saveFailedMessage(FailedMessageException $failedMessage)
    {
        // save to database etc...
        $this->logger->error('Failed message. Saving...', [
            'message' => $failedMessage->getViolatingMessage(),
            'exception' => $failedMessage->getException(),
            'handler_key' => $failedMessage->getHandlerServiceId(),
        ]);
    }
}
