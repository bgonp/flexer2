<?php

declare(strict_types=1);

namespace App\Exception\Family;

use App\Entity\Customer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerAlreadyHasFamilyException extends BadRequestHttpException
{
    private const MESSAGE = 'Customer %s already has a family';

    public static function create(Customer $customer): self
    {
        return new self(sprintf(self::MESSAGE, $customer->getId()));
    }
}
