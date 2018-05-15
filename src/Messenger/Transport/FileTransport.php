<?php

namespace App\Messenger\Transport;

use App\Messenger\Middleware\Configuration\RetryConfiguration;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FileTransport implements TransportInterface
{
    private $filename;
    private $encoder;
    private $decoder;
    private $shouldStop = false;

    public function __construct(string $filename, EncoderInterface $encoder, DecoderInterface $decoder)
    {
        $this->filename = $filename;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function receive(callable $handler): void
    {
        while (!$this->shouldStop) {
            if (!$envelope = $this->getNext()) {
                usleep(200000);

                continue;
            }

            /** @var RetryConfiguration $retryConfig */
            if (($retryConfig = $envelope->get(RetryConfiguration::class)) && $retryConfig->getTimeToRun() > time()) {
                $this->addToQueue($envelope);

                continue;
            }

            $handler($envelope);
        }
    }

    public function stop(): void
    {
        $this->shouldStop = true;
    }

    public function send(Envelope $envelope): void
    {
        $this->addToQueue($envelope);
    }

    private function getNext(): ?Envelope
    {
        $queue = $this->getQueue();

        if (!$next = array_shift($queue)) {
            return null;
        }

        $this->saveQueue($queue);

        return $this->decoder->decode($next);
    }

    private function addToQueue(Envelope $envelope): void
    {
        $queue = $this->getQueue();
        $queue[] = $this->encoder->encode($envelope);
        $this->saveQueue($queue);
    }

    private function getQueue(): array
    {
        if (!file_exists($this->filename)) {
            return [];
        }

        return \json_decode(\file_get_contents($this->filename), true);
    }

    private function saveQueue(array $queue): void
    {
        file_put_contents($this->filename, \json_encode($queue));
    }
}
