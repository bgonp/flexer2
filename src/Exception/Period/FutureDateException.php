<?php

declare(strict_types=1);

namespace App\Exception\Period;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FutureDateException extends BadRequestHttpException
{
    private const MESSAGE = 'Date cannot be future.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
