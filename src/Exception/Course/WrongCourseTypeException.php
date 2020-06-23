<?php

declare(strict_types=1);

namespace App\Exception\Course;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WrongCourseTypeException extends BadRequestHttpException
{
    private const MESSAGE = 'Wrong course type';

    public static function create(): self
    {
        return new self(sprintf(self::MESSAGE));
    }
}
