<?php

namespace App\Exception\Role;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UnsupportedRoleException extends BadRequestHttpException
{
    private const MESSAGE = 'Unsupported role %s';

    public static function fromRole(string $role): self
    {
        return new self(sprintf(self::MESSAGE, $role));
    }
}
