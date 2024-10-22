<?php

declare(strict_types=1);

namespace App\Exception\Family;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomersAlreadyHasTheirOwnFamilyException extends BadRequestHttpException
{
    private const MESSAGE = 'Los alumnos ya pertenecen a diferentes familias.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
