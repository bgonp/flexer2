<?php

declare(strict_types=1);

namespace App\Exception\Session;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DuplicateSessionException extends BadRequestHttpException
{
    private const MESSAGE = 'Unable to insert a duplicate session in database';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
