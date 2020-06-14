<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Session;

class SessionRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Session::class;
    }

    public function find($id, $lockMode = null, $lockVersion = null): Session
    {
        /** @var Session $session */
        $session = parent::find($id, $lockMode, $lockVersion);
        return $session;
    }

    public function findByCourseBetweenDates(Course $course, Session $firstSession = null, Session $lastSession = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.course = :course')
            ->setParameter('course', $course)
            ->orderBy('s.day');
        if ($firstSession) {
            $qb->andWhere('s.day >= :init_date')->setParameter('init_date', $firstSession->getDay());
        }
        if ($lastSession) {
            $qb->andWhere('s.day <= :finish_date')->setParameter('finish_date', $lastSession->getDay());
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param string[] $coursesIds
     * @param string[] $periodsIds
     *
     * @return Session[]
     */
    public function getCounterByCoursesAndPeriodsIds(array $coursesIds, array $periodsIds): array
    {
        return $this->createQueryBuilder('s')
            ->select('identity(s.course) course', 'identity(s.period) period', 'COUNT(s.id) as sessions_count')
            ->where('identity(s.course) IN (:courses_ids)')
            ->andWhere('identity(s.period) IN (:periods_ids)')
            ->setParameter('courses_ids', $coursesIds)
            ->setParameter('periods_ids', $periodsIds)
            ->groupBy('s.course', 's.period')
            ->getQuery()->execute();
    }
    public function save(Session $session, bool $flush = true): void
    {
        $this->saveEntity($session, $flush);
    }
}
