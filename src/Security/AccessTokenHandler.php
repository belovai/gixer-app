<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\InvalidTokenException;
use App\Repository\ProbeRepository;
use App\Repository\UserTokenRepository;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserTokenRepository $tokenRepository,
        private ProbeRepository $probeRepository,
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $userToken = $this->tokenRepository->findOneByToken($accessToken);

        if ($userToken) {
            $this->tokenRepository->lastUsedNow($userToken);

            return new UserBadge($userToken->getUser()->getUserIdentifier());
        }

        $probe = $this->probeRepository->findOneByToken($accessToken);

        if (!$probe) {
            throw new InvalidTokenException('Invalid token');
        }

        $this->probeRepository->lastSeenNow($probe);

        return new UserBadge($probe->getUserIdentifier());
    }
}
