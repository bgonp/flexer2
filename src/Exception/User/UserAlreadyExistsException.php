<?php

namespace App\Exception\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAlreadyExistsException extends BadRequestHttpException
{
    private const MESSAGE = 'User with email %s already exists';

    public static function create(string $email): self
    {
        return new self(sprintf(self::MESSAGE, $email));
    }
}
