<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Course;

class CourseService
{
    public function getFullName(Course $course): string
    {
        return implode(' ', [
            // TODO: Provisional, va mal si hay más de un día o se especifica número de semana
            date('l', strtotime('last monday +'.($course->getDayOfWeek()[0] - 1).' days')),
            $course->getTime()->format('H:i'),
            $course->getPlace()->getName(),
            $course->getDiscipline()->getName(),
            $course->getLevel()->getName(),
            $course->getAge()->getName(),
        ]);
    }
}
