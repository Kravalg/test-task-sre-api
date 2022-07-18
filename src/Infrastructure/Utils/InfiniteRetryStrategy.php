<?php

declare(strict_types=1);

namespace App\Infrastructure\Utils;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;

class InfiniteRetryStrategy implements RetryStrategyInterface
{
    private const WAIT_HOUR_IN_MILLISECONDS = 3600000;

    public function __construct(
        private readonly int $waitingTimeInMilliseconds = self::WAIT_HOUR_IN_MILLISECONDS
    ) {
    }

    public function isRetryable(Envelope $message, \Throwable $throwable = null): bool
    {
        return true;
    }

    public function getWaitingTime(Envelope $message, \Throwable $throwable = null): int
    {
        return $this->waitingTimeInMilliseconds;
    }
}
