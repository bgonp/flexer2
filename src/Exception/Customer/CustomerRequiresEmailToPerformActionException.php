<?php

declare(strict_types=1);

namespace App\Exception\Customer;

use App\Entity\Customer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerRequiresEmailToPerformActionException extends BadRequestHttpException
{
    private const MESSAGE = 'Costumer %s has no email, wich is required to perform this action';

    public static function create(Customer $customer): self
    {
        return new self(sprintf(self::MESSAGE, $customer->getId()));
    }
}
