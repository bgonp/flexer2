<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\Course;
use App\Entity\Customer;
use App\Entity\CustomerPosition;
use App\Entity\Period;
use App\Entity\Staff;
use App\Entity\StaffPosition;
use Doctrine\Persistence\ManagerRegistry;

class AttendanceRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendance::class);
    }

    /** @return Attendance[] */
    public function findByCustomer(Customer $customer): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 's')
            ->join('a.session', 's')
            ->join('a.position', 'p')
            ->where('a.customer = :customer')
            ->andWhere('p INSTANCE OF :position_class')
            ->setParameter('customer', $customer)
            ->setParameter('position_class', $this->getClassQueryData(CustomerPosition::class))
            ->orderBy('s.day')
            ->addOrderBy('s.time')
            ->getQuery()->execute();
    }

    /* @return Attendance[] */
    public function findByStaff(Staff $staff): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 's')
            ->join('a.session', 's')
            ->join('a.position', 'p')
            ->where('a.customer = :staff')
            ->andWhere('p INSTANCE OF :position_class')
            ->setParameter('staff', $staff)
            ->setParameter('position_class', $this->getClassQueryData(StaffPosition::class))
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
