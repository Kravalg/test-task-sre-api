<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Enum\ActionEnum;
use App\Domain\Enum\ScanFileEnum;
use App\Domain\Enum\TriggerEnum;
use App\Domain\Event\JobPerformActionEvent;
use App\Domain\Event\JobStatusCheckEvent;
use App\Domain\Repository\JobRepositoryInterface;
use App\Domain\Repository\RuleRepositoryInterface;
use App\Infrastructure\ApiClient\DebrickedClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class JobService
{
    public function __construct(
        private readonly DebrickedClientInterface $debrickedClient,
        private readonly JobRepositoryInterface $jobRepository,
        private readonly RuleRepositoryInterface $ruleRepository,
        private readonly MessageBusInterface $bus,
        private readonly NotifierInterface $notifier
    ) {
    }

    public function start(int $jobId): void
    {
        $job = $this->jobRepository->find($jobId);

        if (!empty($job)) {
            try {
                $ciUploadId = $this->debrickedClient->scanFiles(
                    $job->getFiles(),
                    $job->getRepositoryName(),
                    $job->getCommitName()
                );

                $this->bus->dispatch(
                    new JobStatusCheckEvent($job->getId(), $ciUploadId)
                );
            } catch (GuzzleException $e) {
                if ($e->getCode() !== 400) {
                    throw $e;
                }
                foreach ($job->getRules() as $rule) {
                    if ($rule->getTrigger() === TriggerEnum::UPLOAD_FAILS->value) {
                        $this->bus->dispatch(
                            new JobPerformActionEvent($rule->getId())
                        );
                    }
                }
            }
        }
    }

    public function checkStatus(int $jobId, string $ciUploadId): void
    {
        $job = $this->jobRepository->find($jobId);

        if (!empty($job)) {
            $check = $this->debrickedClient->checkStatusFileScanning($ciUploadId);

            $trigger = $this->getTriggerByScanFileStatus($check['status']);

            if (!empty($trigger)) {
                foreach ($job->getRules() as $rule) {
                    if ($rule->getTrigger() === $trigger->value) {
                        $this->bus->dispatch(
                            new JobPerformActionEvent(
                                $rule->getId(),
                                $check['stats'] ?? []
                            )
                        );
                    }
                }
            }

            if (in_array($check['status'], [ScanFileEnum::IN_PROGRESS->value, ScanFileEnum::NOT_COMPLETED->value])) {
                throw new \DomainException(
                    sprintf(
                        'Scanning status is "%s". Trying to check later...',
                        $check['status'] ?? 'none'
                    )
                );
            }
        }
    }

    public function performAction(int $ruleId, array $stats): void
    {
        $rule = $this->ruleRepository->find($ruleId);

        if (!empty($rule)) {
            $channel = $this->actionEnumToNotifierChannelMap()[$rule->getAction()] ?? null;

            if ($rule->getTrigger() === TriggerEnum::AMOUNT_OF_VULNERABILITIES_GREATER_THAN->value) {
                if ($stats['vulnerabilitiesFound'] <= $rule->getTriggerValue() ?? 0) {
                    return;
                }
            }

            if ($channel === null) {
                throw new \DomainException('Unknown action: ' . $rule->getAction());
            }

            $this->notifier->send(
                new Notification($rule->getTrigger() . ' ' . ($rule->getTriggerValue() ?? ''), [$channel]),
                new Recipient($rule->getActionValue() ?? '')
            );
        }
    }

    private function getTriggerByScanFileStatus(ScanFileEnum $scanFilesStatus): ?TriggerEnum
    {
        $trigger = null;

        if (isset($this->scanFileEnumToActionEnumMap()[$scanFilesStatus->value])) {
            $trigger = $this->scanFileEnumToActionEnumMap()[$scanFilesStatus->value];
        }

        return $trigger;
    }

    /**
     * @return array<string, TriggerEnum>
     */
    private function scanFileEnumToActionEnumMap(): array
    {
        return [
            ScanFileEnum::COMPLETED->value => TriggerEnum::AMOUNT_OF_VULNERABILITIES_GREATER_THAN,
            ScanFileEnum::IN_PROGRESS->value => TriggerEnum::UPLOAD_IN_PROGRESS,
            ScanFileEnum::NOT_COMPLETED->value => TriggerEnum::UPLOAD_IN_PROGRESS,
            ScanFileEnum::FAILED->value => TriggerEnum::UPLOAD_FAILS,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function actionEnumToNotifierChannelMap(): array
    {
        return [
            ActionEnum::SEND_EMAIL_TO_USER->value => 'email',
            ActionEnum::SEND_MESSAGE_TO_SLACK_CHANNEL->value => 'chat',
        ];
    }
}
