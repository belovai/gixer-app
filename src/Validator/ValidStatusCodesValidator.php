<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidStatusCodesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidStatusCodes) {
            throw new UnexpectedTypeException($constraint, ValidStatusCodes::class);
        }

        if (empty($value) || !is_array($value)) {
            return;
        }

        foreach ($value as $item) {
            if (!is_string($item)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($item))
                    ->addViolation();
                continue;
            }

            if (!preg_match('/^(\d{3})(-(\d{3}))?$/', $item, $matches)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($item))
                    ->addViolation();
                continue;
            }

            if (isset($matches[3])) {
                $start = (int) $matches[1];
                $end = (int) $matches[3];

                if ($start > $end) {
                    $this->context->buildViolation($constraint->invalidRangeMessage)
                        ->setParameter('{{ value }}', $this->formatValue($item))
                        ->addViolation();
                }
            }
        }
    }
}
