<?php

declare(strict_types=1);

namespace App\Infrastructure\DataProvider;

use ApiPlatform\Core\DataProvider\ArrayPaginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Domain\Entity\InMemoryEntityInterface;

final class CollectionDataProvider implements
    ContextAwareCollectionDataProviderInterface,
    RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return \is_a($resourceClass, InMemoryEntityInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var InMemoryEntityInterface $resourceClass */
        $allTypes = $resourceClass::getAll();
        $types = [];

        foreach ($allTypes as $id => $name) {
            /** @psalm-suppress UndefinedClass */
            $types[] = new $resourceClass(
                $id,
                $name
            );
        }

        return new ArrayPaginator(
            $types,
            0,
            count($allTypes)
        );
    }
}
