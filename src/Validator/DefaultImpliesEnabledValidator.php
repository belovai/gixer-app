<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DefaultImpliesEnabledValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DefaultImpliesEnabled) {
            throw new UnexpectedTypeException($constraint, DefaultImpliesEnabled::class);
        }

        if (null === $value || !property_exists($value, 'default') || !property_exists($value, 'enabled')) {
            return;
        }

        if ($value->default === true && $value->enabled !== true) {
            $this->context->buildViolation($constraint->message)
                ->atPath('default')
                ->addViolation();
        }
    }
}
