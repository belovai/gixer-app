<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\ProbeRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DefaultProbeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ProbeRepository $probeRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DefaultProbe) {
            throw new UnexpectedTypeException($constraint, DefaultProbe::class);
        }

        $existingDefaultProbe = $this
            ->probeRepository
            ->createQueryBuilder('m')
            ->where('m.deletedAt is null')
            ->andWhere('m.isDefault = true')
            ->andWhere('m.isEnabled = true')
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($existingDefaultProbe) && $value === false) {
            // No default non deleted probe found, so it must be default
            $this->context->buildViolation($constraint->firstMustBeDefault)
                ->atPath('default')
                ->addViolation();
        }

        if (!is_null($existingDefaultProbe) && $value === true) {
            // A default probe already exists
            $this->context->buildViolation($constraint->defaultAlreadyExists)
                ->atPath('default')
                ->addViolation();
        }
    }
}
