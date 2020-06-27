<?php

declare(strict_types=1);

namespace App\Exception\Period;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DateOutOfPeriodBoundsException extends BadRequestHttpException
{
    private const MESSAGE = 'Date must be between init and finish period dates.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
