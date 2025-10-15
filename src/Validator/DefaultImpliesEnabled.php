<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class DefaultImpliesEnabled extends Constraint
{
    public string $message = 'Default must be enabled.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
