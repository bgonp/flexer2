<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Customer;
use App\Entity\Period;

class CourseRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Course::class;
    }

    /** @return Course[] */
    public function findAll(): array
    {
        /** @var Course[] $courses */
        $courses = $this->findBy([], ['school' => 'ASC', 'dayOfWeek' => 'ASC', 'time' => 'ASC']);

        return $courses;
    }

    /**
     * @param Customer[] $customers
     * @param Period[]   $periods
     *
     * @return Course[]
     */
    // TODO: Quitar si finalmente no se usa
    /*public function getCoursesFromCustomersAndPeriods(array $customers, array $periods): array
    {
        return $this->createQueryBuilder()
            ->select('c')
            ->from('App:Course', 'c')
            ->join('c.assignments', 'a')
            ->join('c.sessions', 's')
            ->where('a.customer IN (:customers)')
            ->andWhere('s.period IN (:periods)')
            ->setParameter('customers', $customers)
            ->setParameter('periods', $periods)
            ->getQuery()->execute();
    }*/

    public function save(Course $course, bool $flush = true): void
    {
        $this->saveEntity($course, $flush);
    }
}
