<?php

namespace App\Repository;

use App\Entity\Probe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Probe>
 */
class ProbeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Probe::class);
    }

    public function findCurrentDefault(): ?Probe
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isDefault = :isDefault')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('isDefault', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
