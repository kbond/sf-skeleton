<?php

namespace App\Messenger\Middleware;

use App\Messenger\Exception\FailedMessageException;
use App\Messenger\Middleware\Configuration\SingleHandlerConfiguration;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\EnvelopeAwareInterface;
use Symfony\Component\Messenger\Handler\Locator\ContainerSingleHandlerLocator;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SingleHandlerMiddleware implements MiddlewareInterface, EnvelopeAwareInterface
{
    private $handlerLocator;

    public function __construct(ContainerSingleHandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
    }

    public function handle($message, callable $next)
    {
        $envelope = Envelope::wrap($message);

        /** @var SingleHandlerConfiguration|null $singleHandlerConfig */
        if ($singleHandlerConfig = $envelope->get(SingleHandlerConfiguration::class)) {
            return $this->handleSingle(
                $this->handlerLocator->resolve($singleHandlerConfig->getHandlerKey()),
                $envelope->getMessage()
            );
        }

        return $next($message);
    }

    private function handleSingle(callable $handler, object $message)
    {
        try {
            return $handler($message);
        } catch (\Throwable $e) {
            throw new FailedMessageException($message, $e, $handler);
        }
    }
}
