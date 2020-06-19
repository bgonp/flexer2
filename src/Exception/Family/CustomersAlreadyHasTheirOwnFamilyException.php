<?php

declare(strict_types=1);

namespace App\Exception\Family;

use App\Entity\Customer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomersAlreadyHasTheirOwnFamilyException extends BadRequestHttpException
{
    private const MESSAGE = 'Customers %s and %s belongs to different families';

    public static function create(Customer $customer, Customer $familiar): self
    {
        return new self(sprintf(self::MESSAGE, $customer->getId(), $familiar->getId()));
    }
}
