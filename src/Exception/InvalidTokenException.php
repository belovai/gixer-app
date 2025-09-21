<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidTokenException extends HttpException
{
    public function __construct($message = 'Invalid token')
    {
        parent::__construct(400, $message);
    }
}
