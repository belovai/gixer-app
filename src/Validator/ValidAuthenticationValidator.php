<?php

declare(strict_types=1);

namespace App\Validator;

use App\Validator\Authentication\AuthenticationTypeValidatorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidAuthenticationValidator extends ConstraintValidator
{
    /** @var iterable<AuthenticationTypeValidatorInterface> */
    private iterable $validators;

    public function __construct(
        #[AutowireIterator('app.auth_type_validator')] iterable $validators,
    ) {
        $this->validators = $validators;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidAuthentication) {
            throw new UnexpectedTypeException($constraint, ValidAuthentication::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_array($value) || count($value) !== 1) {
            $this->context->buildViolation($constraint->invalidStructureMessage)->addViolation();

            return;
        }

        $type = array_key_first($value);
        $credentials = reset($value);

        foreach ($this->validators as $validator) {
            if ($validator->supports($type)) {
                $validator->validate($credentials, $this->context);

                return;
            }
        }

        $this->context->buildViolation($constraint->unsupportedTypeMessage)
            ->setParameter('{{ type }}', $type)
            ->addViolation();
    }
}
