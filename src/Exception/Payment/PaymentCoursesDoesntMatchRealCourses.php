<?php

declare(strict_types=1);

namespace App\Exception\Payment;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentCoursesDoesntMatchRealCourses extends BadRequestHttpException
{
    private const MESSAGE = 'Payment rate courses count doesn\'t match real courses count.';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
