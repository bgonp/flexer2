<?php

declare(strict_types=1);

namespace App\Exception\Listing;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PositionMustBeStaffException extends BadRequestHttpException
{
    private const MESSAGE = 'Position of staff must be a only staff position';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
