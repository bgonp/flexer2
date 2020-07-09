<?php

declare(strict_types=1);

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MissingRequiredFieldsException extends BadRequestHttpException
{
    private const MESSAGE = 'Los siguientes campos son obligatorios pero no contienen datos: %s';

    public static function create(array $fields): self
    {
        return new self(sprintf(self::MESSAGE, implode(', ', $fields)));
    }
}
