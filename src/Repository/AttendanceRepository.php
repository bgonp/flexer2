<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\Course;
use App\Entity\Customer;
use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Session;
use App\Entity\Staff;

class AttendanceRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Attendance::class;
    }

    /** @return Attendance[] */
    public function findByCustomerOrdered(Customer $customer): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 's')
            ->join('a.session', 's')
            ->join('a.position', 'p')
            ->where('a.customer = :customer')
            ->andWhere('p.isStaff = 0')
            ->setParameter('customer', $customer)
            ->orderBy('s.day')
            ->addOrderBy('s.time')
            ->getQuery()->execute();
    }

    /* @return Attendance[] */
    public function findByStaffOrdered(Staff $staff): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 's')
            ->join('a.session', 's')
            ->join('a.position', 'p')
            ->where('a.customer = :staff')
            ->andWhere('p.isStaff = 1')
            ->setParameter('staff', $staff)
            ->orderBy('s.day')
            ->addOrderBy('s.time')
            ->getQuery()->execute();
    }

    /**
     * @param Period[]   $periods
     * @param Customer[] $customers
     * @param Course     $course    null and it won't restrict results by course
     *
     * @return Attendance[]
     */
    public function findUnpaidByCustomersAndPeriods(array $customers, array $periods, ?Course $course = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a', 's')
            ->join('a.session', 's')
            ->where('a.payment IS NULL')
            ->andWhere('a.customer IN (:customers)')
            ->andWhere('s.period IN (:periods)')
            ->setParameter('customers', $customers)
            ->setParameter('periods', $periods);
        if (!is_null($course)) {
            $qb->andWhere('s.course = :course')->setParameter('course', $course);
        }

        return $qb->getQuery()->execute();
    }

    public function save(Attendance $attendance, bool $flush = true): void
    {
        $this->saveEntity($attendance, $flush);
    }
}
