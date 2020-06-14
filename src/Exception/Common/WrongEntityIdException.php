<?php

declare(strict_types=1);

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WrongEntityIdException extends BadRequestHttpException
{
    private const MESSAGE = '%s with ID %s not found.';

    public static function create(string $class, string $id = null): self
    {
        $id = $id ?: 'NULL';
        return new self(sprintf(self::MESSAGE, $class, $id));
    }
}
