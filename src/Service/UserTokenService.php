<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserToken;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserTokenService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserTokenRepository $tokenRepository,
        private readonly RequestStack $requestStack,
    ) {
        //
    }

    public function generateAuthToken(User $user): string
    {
        $this->revokeAllTokens($user);
        $this->entityManager->flush();

        $token = $this->generateToken();
        $tokenEntity = new UserToken($user, hash('sha256', ($token)));
        $tokenEntity->setUserAgent($this->requestStack->getCurrentRequest()?->headers->get('User-Agent'));
        $tokenEntity->setIpAddress($this->requestStack->getCurrentRequest()?->getClientIp());

        $this->entityManager->persist($tokenEntity);
        $this->entityManager->flush();

        return $token;
    }

    public function revokeAllTokens(User $user): int
    {
        return $this->tokenRepository->revokeAllTokens($user);
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

}
