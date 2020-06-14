<?php

namespace App\Exception\Role;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequiredRoleToAddRoleAdminNotFoundException extends BadRequestHttpException
{
    private const MESSAGE = '%s required to perform this operation';

    public static function fromRole(string $role): self
    {
        return new self(sprintf(self::MESSAGE, $role));
    }
}
