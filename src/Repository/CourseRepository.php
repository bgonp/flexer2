<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Customer;
use App\Entity\Period;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends PaginableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /** @return Course[] */
    public function findAll(int $page = 1): array
    {
        /** @var Course[] $courses */
        $courses = $this->findBy(
            [],
            ['school' => 'ASC', 'dayOfWeek' => 'ASC', 'time' => 'ASC'],
            self::PER_PAGE,
            $this->getOffset($page)
        );

        return $courses;
    }

    /** @return Course[] */
    public function findBySearchTerm(string $search, int $page = null): array
    {
        $terms = explode(' ', trim($search));
        // TODO: No filtra por dia de la semana
        $query = $this->createQueryBuilder('c')
            ->select('c', 's', 'p', 'd', 'l', 'a', 'z')
            ->join('c.school', 's')
            ->join('c.discipline', 'd')
            ->join('c.level', 'l')
            ->join('c.age', 'a')
            ->join('c.place', 'p')
            ->join('p.zone', 'z')
            ->setFirstResult($this->getOffset($page))
            ->setMaxResults(self::PER_PAGE);
        foreach ($terms as $index => $term) {
            $query
                ->andWhere("CONCAT_WS(' ', WEEK_DAY_ES(c.dayOfWeek), DATE_FORMAT(c.time, '%H:%i'), s.name, p.name, d.name, l.name, a.name, z.name) LIKE :search$index")
                ->setParameter("search$index", "%$term%");
        }

        return $query->getQuery()->execute();
    }

    public function getLastPageBySearchTerm(string $search): int
    {
        $search = '%'.str_replace(' ', '%', $search).'%';
        $count = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->join('c.school', 's')
            ->join('c.discipline', 'd')
            ->join('c.level', 'l')
            ->join('c.age', 'a')
            ->join('c.place', 'p')
            ->join('p.zone', 'z')
            ->where('CONCAT_WS(\' \', s.name, p.name, d.name, l.name, a.name, z.name) LIKE :search')
            ->setParameter('search', $search)
            ->getQuery()->getSingleScalarResult();

        return (int) ceil($count / self::PER_PAGE) ?: 1;
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

    public function remove(Course $course, bool $flush = true): void
    {
        $this->removeEntity($course, $flush);
    }
}
