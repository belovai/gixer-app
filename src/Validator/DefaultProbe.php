<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class DefaultProbe extends Constraint
{
    public string $defaultAlreadyExists = 'The default probe already exists.';
    public string $firstMustBeDefault = 'The first probe must default.';
}
