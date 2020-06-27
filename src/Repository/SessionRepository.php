<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Session;
use Doctrine\Persistence\ManagerRegistry;

class SessionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /** @return Session[] */
    public function findByListingAndPeriod(Listing $listing, Period $period): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.listing = :listing')
            ->andWhere('s.period = :period')
            ->orderBy('s.day')
            ->setParameter('listing', $listing)
            ->setParameter('period', $period)
            ->getQuery()->execute();
    }

    /** @return Session[] */
    public function findByCourseBetweenSessions(
        Course $course,
        Session $firstSession = null,
        Session $lastSession = null
    ): array {
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

    /** @return Session[] */
    public function findByPeriodAndDate(Period $period, \DateTime $date): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'a')
            ->leftJoin('s.attendances', 'a')
            ->where('s.period = :period')
            ->andWhere('s.day = :date')
            ->setParameter('period', $period)
            ->setParameter('date', $date)
            ->getQuery()->execute();
    }

    /**
     * @param string[] $coursesIds
     * @param string[] $periodsIds
     *
     * @return array 'course' => course_id, 'period' => period_id, 'sessions_count' => COUNT(session)
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

    public function remove(Session $session, bool $flush = true): void
    {
        $this->removeEntity($session, $flush);
    }
}
