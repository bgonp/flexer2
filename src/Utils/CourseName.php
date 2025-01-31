<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Course;

class CourseName
{
    public static function get(Course $course): string
    {
        return implode(' ', [
            // TODO: Provisional, va mal si hay más de un día o se especifica número de semana
            ucfirst(\IntlDateFormatter::formatObject(new \DateTime('last monday +'.($course->getDayOfWeek()[0] - 1).' days'), 'EEEE')),
            $course->getTime()->format('H:i'),
            $course->getPlace() ? $course->getPlace()->getName() : '',
            $course->getDiscipline() ? $course->getDiscipline()->getName() : '',
            $course->getLevel() ? $course->getLevel()->getName() : '',
            $course->getAge() ? $course->getAge()->getName() : '',
        ]);
    }
}