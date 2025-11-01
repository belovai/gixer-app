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

    public function findOneByToken(string $token): ?Probe
    {
        $token = hash('sha256', $token);

        return $this->createQueryBuilder('p')
            ->andWhere('p.token = :token')
            ->andWhere('p.deletedAt IS NULL')
            ->andWhere('p.isEnabled = :isEnabled')
            ->setParameter('token', $token)
            ->setParameter('isEnabled', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function lastSeenNow(Probe $probe): void
    {
        $probe->setLastSeenAt(new \DateTimeImmutable());
        $this->getEntityManager()->flush();
    }
}
