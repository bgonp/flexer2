<?php

declare(strict_types=1);

namespace App\Exception\Payment;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentSessionsDoesntMatchPeriodSessions extends BadRequestHttpException
{
    private const MESSAGE = 'Payment affected sessions doesn\'t match real period sessions.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
