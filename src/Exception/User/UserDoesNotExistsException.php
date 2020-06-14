<?php

namespace App\Exception\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserDoesNotExistsException extends BadRequestHttpException
{
    private const MESSAGE = 'User with id %s does not exists';

    public static function fromUserId(string $id): self
    {
        return new self(sprintf(self::MESSAGE, $id));
    }
}
