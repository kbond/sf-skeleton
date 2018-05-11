<?php

namespace App\Messenger\Transport;

use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FileTransportFactory implements TransportFactoryInterface
{
    private $encoder;
    private $decoder;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function createTransport(string $dsn, array $options): TransportInterface
    {
        return new FileTransport($dsn, $this->encoder, $this->decoder);
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'file://');
    }
}
