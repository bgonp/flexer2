<?php

declare(strict_types=1);

namespace App\Exception\Listing;

use App\Entity\Course;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CourseAlreadyHasListing extends BadRequestHttpException
{
    private const MESSAGE = 'Course %s already has a listing';

    public static function create(Course $course): self
    {
        return new self(sprintf(self::MESSAGE, $course->getId()));
    }
}
