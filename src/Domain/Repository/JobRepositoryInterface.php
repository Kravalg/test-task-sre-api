<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Job;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * @psalm-suppress MoreSpecificImplementedParamType
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface JobRepositoryInterface extends ObjectRepository, Selectable
{
}
