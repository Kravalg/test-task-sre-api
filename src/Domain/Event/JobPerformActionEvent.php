<?php

declare(strict_types=1);

namespace App\Domain\Event;

class JobPerformActionEvent
{
    public function __construct(
        private readonly int $ruleId,
        private readonly array $stats = []
    ) {
    }

    public function getRuleId(): int
    {
        return $this->ruleId;
    }

    public function getStats(): array
    {
        return $this->stats;
    }
}
