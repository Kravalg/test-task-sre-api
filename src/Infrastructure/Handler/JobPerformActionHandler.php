<?php

declare(strict_types=1);

namespace App\Infrastructure\Handler;

use App\Domain\Event\JobPerformActionEvent;
use App\Domain\Service\JobService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JobPerformActionHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly JobService $jobService
    ) {
    }

    public function __invoke(JobPerformActionEvent $event)
    {
        $this->jobService->performAction($event->getRuleId(), $event->getStats());
    }
}
