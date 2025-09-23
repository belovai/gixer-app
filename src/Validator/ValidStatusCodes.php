<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidStatusCodes extends Constraint
{
    public string $message = 'Invalid "{{ value }}". It must be single values or ranges (eg. "200" or "400-499").';
    public string $invalidRangeMessage = 'Invalid range.';
}
