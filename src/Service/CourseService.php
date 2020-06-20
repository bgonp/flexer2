<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Course;

class CourseService
{
    public function getFullName(Course $course): string
    {
        setlocale(LC_ALL, 'es_ES');

        return implode(' ', [
            // TODO: Provisional, va mal si hay más de un día o se especifica número de semana
            ucfirst(\IntlDateFormatter::formatObject(new \DateTime('last monday +'.($course->getDayOfWeek()[0] - 1).' days'), 'EEEE')),
            $course->getTime()->format('H:i'),
            $course->getPlace()->getName(),
            $course->getDiscipline()->getName(),
            $course->getLevel()->getName(),
            $course->getAge()->getName(),
        ]);
    }
}
