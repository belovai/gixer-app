<?php

declare(strict_types=1);

namespace App\Tests;

use App\Factory\UserTokenFactory;

trait HelpersTrait
{
    public function userToken(): string
    {
        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        return $token;
    }
}
