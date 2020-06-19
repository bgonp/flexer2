<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Period;
use Doctrine\Persistence\ManagerRegistry;

class PeriodRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Period::class);
    }

    public function getConsecutivePeriods(int $count, Period $since)
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
}
