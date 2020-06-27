<?php

declare(strict_types=1);

namespace App\Repository;

use App\DQL\PaginableQuery;
use App\Entity\Course;
use App\Entity\School;
use App\Entity\Season;
use Doctrine\Persistence\ManagerRegistry;

class SeasonRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function findAllPaged(int $page = 1): PaginableQuery
    {
        return $this->findBySchoolPaged(null, $page);
    }

    public function findBySchoolPaged(?School $school, int $page = 1): PaginableQuery
    {
        $query = $this->createQueryBuilder('s')
            ->select('s', 'sc')
            ->join('s.school', 'sc')
            ->orderBy('s.initDate', 'DESC')
            ->addOrderBy('s.finishDate', 'DESC')
            ->addOrderBy('sc.name', 'ASC');
        if ($school) {
            $query->where('s.school = :school')->setParameter('school', $school);
        }

        return new PaginableQuery($query->getQuery(), $page);
    }

    /** @return Season[] */
    public function findByCourseWithPeriods(Course $course): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'p')
            ->join('s.periods', 'p')
            ->join('p.sessions', 'se')
            ->where('se.course = :course')
            ->setParameter('course', $course)
            ->orderBy('s.initDate', 'DESC')
            ->addOrderBy('p.initDate', 'DESC')
            ->getQuery()->execute();
    }

    public function save(Season $season, bool $flush = true): void
    {
        $this->saveEntity($season, $flush);
    }

    public function remove(Season $season, bool $flush = true): void
    {
        $this->removeEntity($season, $flush);
    }
}
