<?php

declare(strict_types=1);

namespace App\Exception\Customer;

use App\Entity\Customer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerAlreadyHasUserException extends BadRequestHttpException
{
    private const MESSAGE = 'Customer %s already has a user';

    public static function create(Customer $customer): self
    {
        return new self(sprintf(self::MESSAGE, $customer->getId()));
    }
}
