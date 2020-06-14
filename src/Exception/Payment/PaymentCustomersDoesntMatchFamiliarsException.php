<?php

declare(strict_types=1);

namespace App\Exception\Payment;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentCustomersDoesntMatchFamiliarsException extends BadRequestHttpException
{
    private const MESSAGE = 'Payment rate customers count doesn\'t match number of familiars';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
