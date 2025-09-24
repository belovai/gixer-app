<?php

declare(strict_types=1);

namespace App\Enum;

enum HttpMethodEnum: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Delete = 'DELETE';
    case Patch = 'PATCH';
    case Options = 'OPTIONS';
    case Head = 'HEAD';
    case Trace = 'TRACE';
    case Connect = 'CONNECT';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
