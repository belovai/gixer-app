<?php
declare(strict_types=1);

namespace App\Enum;

enum MetricStatusEnum: string
{
    case pending = 'pending';
    case processing = 'processing';
    case success = 'success';
    case failure = 'failure';
    case abort = 'abort';
}
