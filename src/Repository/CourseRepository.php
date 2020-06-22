<?php

declare(strict_types=1);

namespace App\Repository;

use App\DQL\PaginableQuery;
use App\Entity\Course;
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

    public function findBySearchTermPaged(string $search, string $type = 'active', int $page = 1): PaginableQuery
    {
        $query = $this->addSearchTerms($search, $this->addTypeTerm($type, $this->getMainQuery()));

        return new PaginableQuery($query->getQuery(), $page);
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
