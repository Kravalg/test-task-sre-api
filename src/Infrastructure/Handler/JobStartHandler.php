<?php

declare(strict_types=1);

namespace App\Infrastructure\Handler;

use App\Domain\Event\JobStartEvent;
use App\Domain\Service\JobService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JobStartHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly JobService $jobService
    ) {
    }

    public function __invoke(JobStartEvent $event)
    {
        $this->jobService->start($event->getJobId());
    }
}
