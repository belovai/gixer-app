<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\ProbeRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class FirstProbeIsEnabledValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ProbeRepository $probeRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FirstProbeIsEnabled) {
            throw new UnexpectedTypeException($constraint, FirstProbeIsEnabled::class);
        }

        $existingProbesCount = $this->probeRepository->count();

        if ($existingProbesCount === 0 && $value === false) {
            $this->context->buildViolation($constraint->message)
                ->atPath('default')
                ->addViolation();
        }
    }
}
