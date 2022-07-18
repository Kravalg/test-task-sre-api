<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum ActionEnum: string
{
    case SEND_EMAIL_TO_USER = 'Send an email to user';
    case SEND_MESSAGE_TO_SLACK_CHANNEL = 'Send a message to a Slack channel';
}
