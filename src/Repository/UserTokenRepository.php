<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserToken>
 */
class UserTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToken::class);
    }

    public function findOneByValue(string $token): ?UserToken
    {
        $token = hash('sha256', $token);

        return $this->createQueryBuilder('t')
            ->andWhere('t.token = :token')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function lastUsedNow(UserToken $token): void
    {
        $token->setLastUsedAt(new \DateTimeImmutable());
        $this->getEntityManager()->flush();
    }

    public function revokeAllTokens(User $user): int
    {
        return $this->createQueryBuilder('t')
            ->update(UserToken::class, 't')
            ->set('t.deletedAt', ':now')
            ->where('t.user = :user')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
