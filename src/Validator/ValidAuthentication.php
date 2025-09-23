<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidAuthentication extends Constraint
{
    public string $invalidStructureMessage = 'The authentication data must contain a single key-value pair (e.g., "Basic": {...}).';
    public string $unsupportedTypeMessage = 'The "{{ type }}" authentication type is not supported.';
}
