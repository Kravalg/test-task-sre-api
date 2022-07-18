<?php

declare(strict_types=1);

namespace App\Domain\Event;

class JobStartEvent
{
    public function __construct(
        private readonly int $jobId
    ) {
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }
}
