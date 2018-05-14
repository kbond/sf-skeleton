<?php

namespace App\Messenger\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ChainFailedMessageException extends \RuntimeException
{
    private $failedMessages;

    public function __construct(FailedMessageException ...$failedMessage)
    {
        $this->failedMessages = $failedMessage;

        parent::__construct();
    }

    /**
     * @return FailedMessageException[]
     */
    public function getFailedMessages(): array
    {
        return $this->failedMessages;
    }
}
