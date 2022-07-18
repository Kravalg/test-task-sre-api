<?php

declare(strict_types=1);

namespace App\Infrastructure\Handler;

use App\Domain\Event\JobStatusCheckEvent;
use App\Domain\Service\JobService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JobStatusCheckHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly JobService $jobService
    ) {
    }

    public function __invoke(JobStatusCheckEvent $event)
    {
        $this->jobService->checkStatus($event->getJobId(), $event->getCiUploadId());
    }
}
