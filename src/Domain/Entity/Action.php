<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Enum\ActionEnum;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: [
        'groups' => [Action::GROUP_READ]
    ],
)]
class Action implements InMemoryEntityInterface
{
    public const GROUP_READ = 'trigger:read';

    public function __construct(
        private readonly int $id,
        private readonly string $name
    ) {
    }

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_READ])]
    public function getId(): int
    {
        return $this->id;
    }

    #[Groups([self::GROUP_READ])]
    public function getName(): string
    {
        return $this->name;
    }

    public static function getAll(): array
    {
        return [
            ActionEnum::SEND_EMAIL_TO_USER->value,
            ActionEnum::SEND_MESSAGE_TO_SLACK_CHANNEL->value
        ];
    }
}
