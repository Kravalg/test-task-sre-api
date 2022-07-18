<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum TriggerEnum: string
{
    case AMOUNT_OF_VULNERABILITIES_GREATER_THAN = 'Amount of vulnerabilities found during a scan is greater than';
    case UPLOAD_IN_PROGRESS = 'Upload is in progress';
    case UPLOAD_FAILS = 'Upload fails for some reason';
}
