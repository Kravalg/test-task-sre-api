<?php

declare(strict_types=1);

namespace App\Domain\Event;

class JobStatusCheckEvent
{
    public function __construct(
        private readonly int $jobId,
        private readonly string $ciUploadId,
    ) {
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }

    public function getCiUploadId(): string
    {
        return $this->ciUploadId;
    }
}
