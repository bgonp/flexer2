<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Period;
use App\Entity\Season;
use Doctrine\Persistence\ManagerRegistry;

class PeriodRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Period::class);
    }

    /** @return Period[] */
    public function findBySeason(Season $season): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.season = :season')
            ->setParameter('season', $season)
            ->orderBy('p.initDate', 'ASC')
            ->getQuery()->execute();
    }

    /** @return Period[] */
    public function findConsecutivePeriods(int $count, Period $since): array
    {
        if (1 === $count) {
            return [$since];
        }

        return $this->createQueryBuilder('p')
            ->where('p.season = :season')
            ->andWhere('p.initDate >= :init_date')
            ->setParameter('season', $since->getSeason())
            ->setParameter('init_date', $since->getInitDate())
            ->orderBy('p.initDate')
            ->setMaxResults($count ?: null)
            ->getQuery()->execute();
    }

    public function save(Period $period, bool $flush = true): void
    {
        $this->saveEntity($period, $flush);
    }

    public function remove(Period $period, bool $flush = true): void
    {
        $this->removeEntity($period, $flush);
    }
}
