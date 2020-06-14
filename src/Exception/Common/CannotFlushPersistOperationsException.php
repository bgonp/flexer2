<?php

declare(strict_types=1);

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CannotFlushPersistOperationsException extends BadRequestHttpException
{
    private const MESSAGE = 'Cannot flush persist operations.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
