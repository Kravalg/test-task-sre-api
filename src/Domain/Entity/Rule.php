<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    denormalizationContext: [
        'groups' => [Rule::GROUP_WRITE, Job::GROUP_WRITE]
    ],
    normalizationContext: [
        'groups' => [Rule::GROUP_READ, Job::GROUP_READ]
    ],
)]
class Rule
{
    public const GROUP_READ = 'rule:read';
    public const GROUP_WRITE = 'rule:write';

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(name: 'trigger_name', type: Types::STRING)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE, Job::GROUP_READ, Job::GROUP_WRITE])]
    #[Assert\NotBlank]
    private string $trigger;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE, Job::GROUP_READ, Job::GROUP_WRITE])]
    private ?string $triggerValue = null;

    #[ORM\Column(name: 'action_name', type: Types::STRING)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE, Job::GROUP_READ, Job::GROUP_WRITE])]
    #[Assert\NotBlank]
    private string $action;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE, Job::GROUP_READ, Job::GROUP_WRITE])]
    private ?string $actionValue = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'rules')]
    private Job $job;

    /**
     * @psalm-suppress InvalidNullableReturnType, NullableReturnStatement
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }

    public function setTrigger(string $trigger): void
    {
        $this->trigger = $trigger;
    }

    public function getTriggerValue(): ?string
    {
        return $this->triggerValue;
    }

    public function setTriggerValue(?string $triggerValue): void
    {
        $this->triggerValue = $triggerValue;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getActionValue(): ?string
    {
        return $this->actionValue;
    }

    public function setActionValue(?string $actionValue): void
    {
        $this->actionValue = $actionValue;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): void
    {
        $this->job = $job;
    }
}
