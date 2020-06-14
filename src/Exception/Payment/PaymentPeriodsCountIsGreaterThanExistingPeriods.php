<?php

declare(strict_types=1);

namespace App\Exception\Payment;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentPeriodsCountIsGreaterThanExistingPeriods extends BadRequestHttpException
{
    private const MESSAGE = 'Payment rate periods count is greater than existing periods.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
