<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum ScanFileEnum: string
{
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';
    case IN_PROGRESS = 'in_progress';
    case FAILED = 'failed';
}
