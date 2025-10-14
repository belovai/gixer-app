<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class FirstProbeIsDefault extends Constraint
{
    public string $message = 'The first probe must be set as default.';
}
