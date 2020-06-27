<?php

declare(strict_types=1);

namespace App\Exception\Assignment;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CannotAssignStaffPositionToCustomerException extends BadRequestHttpException
{
    private const MESSAGE = 'Cannot assign a non staff customer with a staff position';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
