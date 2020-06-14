<?php

declare(strict_types=1);

namespace App\Exception\Course;

use App\Entity\Course;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CourseIsNotActiveException extends BadRequestHttpException
{
    private const MESSAGE = 'Course %s is not active';

    public static function create(Course $course): self
    {
        return new self(sprintf(self::MESSAGE, $course->getId()));
    }
}
