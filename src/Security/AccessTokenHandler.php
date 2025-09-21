<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\InvalidTokenException;
use App\Repository\UserTokenRepository;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private UserTokenRepository $tokenRepository)
    {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->tokenRepository->findOneByValue($accessToken);

        if (!$token) {
            throw new InvalidTokenException('Invalid token');
        }

        $token->setLastUsedAt(new \DateTimeImmutable());
        $this->tokenRepository->lastUsedNow($token);

        return new UserBadge($token->getUser()->getUserIdentifier());
    }
}
