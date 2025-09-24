<?php

declare(strict_types=1);

namespace App\Validator\Authentication;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BasicAuthValidator implements AuthenticationTypeValidatorInterface
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function supports(string $type): bool
    {
        return 'Basic' === $type;
    }

    public function validate(array $credentials, ExecutionContextInterface $context): void
    {
        $constraints = new Assert\Collection([
            'fields' => [
                'username' => [new Assert\NotBlank(), new Assert\Type('string')],
                'password' => [new Assert\NotBlank(), new Assert\Type('string')],
            ],
            'allowExtraFields' => false,
        ]);

        $errors = $this->validator->validate($credentials, $constraints);

        foreach ($errors as $error) {
            $context->buildViolation($error->getMessage())
                ->atPath('Basic.'.$error->getPropertyPath())
                ->addViolation();
        }
    }
}
