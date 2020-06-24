<?php

declare(strict_types=1);

namespace App\Exception\Season;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SeasonAlreadyHasPeriodsException extends BadRequestHttpException
{
    private const MESSAGE = 'Season already has periods';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
