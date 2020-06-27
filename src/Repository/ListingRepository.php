<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Staff;
use App\Entity\StaffPosition;
use Doctrine\Persistence\ManagerRegistry;

class ListingRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    /** @return Listing[] */
    public function findByStaffStaffPositionAndPeriod(Staff $staff, StaffPosition $position, Period $period): array
    {
        return $this->createQueryBuilder('l')
            ->select('l', 's')
            ->join('l.sessions', 's')
            ->join('s.attendances', 'a')
            ->where('s.period = :period')
            ->andWhere('a.customer = :staff')
            ->andWhere('a.position = :position')
            ->setParameter('period', $period)
            ->setParameter('staff', $staff)
            ->setParameter('position', $position)
            ->orderBy('s.day')
            ->addOrderBy('s.time')
            ->getQuery()->execute();
    }

    /** @return Listing[] */
    public function findByPeriod(Period $period): array
    {
        return $this->createQueryBuilder('l')
            ->select('l', 'c')
            ->join('l.courses', 'c')
            ->join('l.sessions', 's')
            ->where('s.period = :period')
            ->setParameter('period', $period)
            ->getQuery()->execute();
    }

    public function save(Listing $listing, bool $flush = true): void
    {
        $this->saveEntity($listing, $flush);
    }

    public function remove(Listing $listing, bool $flush = true): void
    {
        $this->removeEntity($listing, $flush);
    }
}
