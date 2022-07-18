<?php

declare(strict_types=1);

namespace App\Domain\Entity;

interface InMemoryEntityInterface
{
    public static function getAll(): array;
}
