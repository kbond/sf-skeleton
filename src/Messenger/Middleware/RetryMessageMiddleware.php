<?php

namespace App\Messenger\Middleware;

use App\Messenger\Exception\ChainFailedMessageException;
use App\Messenger\Exception\FailedMessageException;
use App\Messenger\Exception\RetryMessageException;
use App\Messenger\Middleware\Configuration\RetryConfiguration;
use App\Messenger\Middleware\Configuration\SingleHandlerConfiguration;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\EnvelopeAwareInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RetryMessageMiddleware implements MiddlewareInterface, EnvelopeAwareInterface, ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedServices()
    {
        return [
            LoggerInterface::class,
            MessageBusInterface::class,
        ];
    }

    public function handle($message, callable $next)
    {
        $envelope = Envelope::wrap($message);

        /** @var RetryConfiguration|null $retryConfig */
        $retryConfig = $envelope->get(RetryConfiguration::class);

        /** @var SingleHandlerConfiguration|null $singleHandlerConfig */
        $singleHandlerConfig = $envelope->get(SingleHandlerConfiguration::class);

        if ($retryConfig && $singleHandlerConfig) {
            return $this->doHandle($message, $next, $retryConfig->getAttempt());
        }

        // not a retry
        return $this->doHandle($message, $next);
    }

    private function doHandle(object $message, callable $next, int $attempt = 1)
    {
        try {
            return $next($message);
        } catch (FailedMessageException $e) {
            return $this->handleFailedMessage($e, $attempt);
        } catch (ChainFailedMessageException $e) {
            // cannot be a retry attempt as the SingleHandlerMiddleware only has a single handler
            return $this->handleChainFailedMessage($e);
        }
    }

    private function handleChainFailedMessage(ChainFailedMessageException $exception)
    {
        $errors = [];

        foreach ($exception->getFailedMessages() as $failedMessage) {
            try {
                $this->handleFailedMessage($failedMessage, 1);
            } catch (FailedMessageException $e) {
                $errors[] = $e;
            }
        }

        if (\count($errors)) {
            throw new ChainFailedMessageException(...$errors);
        }

        return null;
    }

    private function handleFailedMessage(FailedMessageException $failedMessage, int $attempt)
    {
        $exception = $failedMessage->getException();

        if (!$exception instanceof RetryMessageException) {
            throw $failedMessage;
        }

        if ($attempt >= $exception->getMaxAttempts()) {
            // throw FailedTooManyTimesException?
            throw $failedMessage;
        }

        $this->container->get(LoggerInterface::class)->warning('Retrying failed message', [
            'message' => $failedMessage->getViolatingMessage(),
            'exception' => $exception->getMessage(),
            'previous_exception' => $exception->getPrevious(),
            'handler_key' => $failedMessage->getHandlerServiceId(),
            'attempt' => $attempt,
        ]);

        $this->container->get(MessageBusInterface::class)->dispatch(
            Envelope::wrap($failedMessage->getViolatingMessage())
                ->with(new RetryConfiguration(++$attempt, $exception->getDelayInSeconds()))
                ->with(new SingleHandlerConfiguration($failedMessage->getHandlerServiceId()))
        );

        return null;
    }
}
