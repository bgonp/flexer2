<?php

declare(strict_types=1);

namespace App\Exception\Attendance;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DuplicateAttendanceException extends BadRequestHttpException
{
    private const MESSAGE = 'Unable to insert a duplicate attendance in database';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
