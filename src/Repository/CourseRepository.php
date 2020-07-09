<?php

declare(strict_types=1);

namespace App\Repository;

use App\DQL\PaginableQuery;
use App\Entity\Course;
use App\Entity\School;
use App\Exception\Course\WrongCourseTypeException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findAllPaged(string $type = 'active', int $page = 1): PaginableQuery
    {
        $query = $this->addTypeTerm($type, $this->getMainQuery());

        return new PaginableQuery($query->getQuery(), $page);
    }

    /** @return Course[] */
    public function findBySearchTerm(string $search, string $type = 'active'): array
    {
        $query = $this->addSearchTerms($search, $this->addTypeTerm($type, $this->getMainQuery()));

        return $query->getQuery()->execute();
    }

    /** @return Course[] */
    public function findCompatibleBySearchTerm(Course $course, string $search, string $type = 'active'): array
    {
        $query = $this->addSearchTerms($search, $this->addTypeTerm($type, $this->getMainQuery()));

        return $query
            ->andWhere('c.dayOfWeek = :dayOfWeek')
            ->andWhere('c.time = :time')
            ->andWhere('c.school = :school')
            ->andWhere('c.place = :place')
            ->setParameter('dayOfWeek', $course->getDayOfWeek())
            ->setParameter('time', $course->getTime())
            ->setParameter('school', $course->getSchool())
            ->setParameter('place', $course->getPlace())
            ->getQuery()->execute();
    }

    public function findBySearchTermPaged(string $search, string $type = 'active', int $page = 1): PaginableQuery
    {
        $query = $this->addSearchTerms($search, $this->addTypeTerm($type, $this->getMainQuery()));

        return new PaginableQuery($query->getQuery(), $page);
    }

    /** @return Course[] */
    public function findBySchoolAndDate(School $school, \DateTime $date): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->where('c.school = :school')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('c.initDate'),
                $qb->expr()->lte('c.initDate', ':date')
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('c.finishDate'),
                $qb->expr()->gte('c.finishDate', ':date')
            ))
            ->setParameter('school', $school)
            ->setParameter('date', $date)
            ->getQuery()->execute();
    }

    public function save(Course $course, bool $flush = true): void
    {
        $this->saveEntity($course, $flush);
    }

    public function remove(Course $course, bool $flush = true): void
    {
        $this->removeEntity($course, $flush);
    }

    private function getMainQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c', 's', 'd', 'l', 'a', 'p', 'z')
            ->join('c.school', 's')
            ->leftJoin('c.discipline', 'd')
            ->leftJoin('c.level', 'l')
            ->leftJoin('c.age', 'a')
            ->leftJoin('c.place', 'p')
            ->leftJoin('p.zone', 'z')
            ->orderBy('s.name', 'ASC')
            ->addOrderBy('c.dayOfWeek', 'ASC')
            ->addOrderBy('c.time', 'ASC');
    }

    private function addSearchTerms(string $search, QueryBuilder $query): QueryBuilder
    {
        $terms = explode(' ', trim($search));
        foreach ($terms as $index => $term) {
            $query
                ->andWhere("SEARCH_STRING() LIKE :search$index")
                ->setParameter("search$index", "%$term%");
        }
        //dd($query->getQuery());

        return $query;
    }

    private function addTypeTerm(string $type, QueryBuilder $query): QueryBuilder
    {
        if (!in_array($type, ['all', 'inactive', 'active'])) {
            throw WrongCourseTypeException::create();
        }
        if ('all' !== $type) {
            $query
                ->where('c.isActive = :active')
                ->setParameter('active', 'active' === $type);
        }

        return $query;
    }
}
