<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Rule;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * @psalm-suppress MoreSpecificImplementedParamType
 * @method Rule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rule[]    findAll()
 * @method Rule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface RuleRepositoryInterface extends ObjectRepository, Selectable
{
}
