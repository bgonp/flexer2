<?php

declare(strict_types=1);

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PageOutOfBoundsException extends BadRequestHttpException
{
    private const MESSAGE = 'Page number out of bounds';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
