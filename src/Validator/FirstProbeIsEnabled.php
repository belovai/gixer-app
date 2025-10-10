<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class FirstProbeIsEnabled extends Constraint
{
    public string $message = 'The first probe must be enabled.';
}
