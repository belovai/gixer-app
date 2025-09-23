<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidHeadersValidator extends ConstraintValidator
{
    public function validate(mixed $headers, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidHeaders) {
            throw new UnexpectedTypeException($constraint, ValidHeaders::class);
        }

        if (empty($headers) || !is_array($headers)) {
            return;
        }

        foreach ($headers as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ key }}', (string) $key)
                    ->atPath('httpHeaders')
                    ->addViolation();
                break;
            }
        }
    }
}
