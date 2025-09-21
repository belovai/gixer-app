<?php
declare(strict_types=1);

namespace App\DTO\User;

class AuthResponseDto
{
    public function __construct(
        public string $token,
    ) {
        //
    }
}
