<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Listener;

use App\Domain\Entity\Job;
use App\Domain\Event\JobStartEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class NewJobListener
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
    }

    public function postPersist(Job $job): void
    {
        $this->bus->dispatch(
            new JobStartEvent($job->getId())
        );
    }
}
