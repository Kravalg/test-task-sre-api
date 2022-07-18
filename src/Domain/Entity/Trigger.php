<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Enum\TriggerEnum;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: [
        'groups' => [Trigger::GROUP_READ]
    ]
)]
class Trigger implements InMemoryEntityInterface
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
            TriggerEnum::AMOUNT_OF_VULNERABILITIES_GREATER_THAN->value,
            TriggerEnum::UPLOAD_FAILS->value,
            TriggerEnum::UPLOAD_IN_PROGRESS->value
        ];
    }
}
