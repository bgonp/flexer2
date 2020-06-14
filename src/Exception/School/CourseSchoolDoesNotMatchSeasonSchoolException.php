<?php

declare(strict_types=1);

namespace App\Exception\School;

use App\Entity\Course;
use App\Entity\Season;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CourseSchoolDoesNotMatchSeasonSchoolException extends BadRequestHttpException
{
    private const MESSAGE = 'Course %s school and Season %s school are different';

    public static function create(Course $course, Season $season): self
    {
        return new self(sprintf(self::MESSAGE, $course->getId(), $season->getId()));
    }
}
