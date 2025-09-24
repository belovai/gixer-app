<?php

declare(strict_types=1);

namespace App\Validator\Authentication;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

interface AuthenticationTypeValidatorInterface
{
    public function supports(string $type): bool;

    public function validate(array $credentials, ExecutionContextInterface $context): void;
}
