<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Assignment;
use App\Entity\Course;
use App\Entity\Customer;
use Doctrine\Persistence\ManagerRegistry;

class AssignmentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    /** @return Assignment[] */
    public function findByCourseAndDate(Course $course, \DateTime $date): array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->leftJoin('a.firstSession', 'fs')
            ->leftJoin('a.lastSession', 'ls')
            ->where('a.course = :course')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('fs'),
                $qb->expr()->lte('fs.day', ':date')
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('ls'),
                $qb->expr()->gte('ls.day', ':date')
            ))
            ->setParameter('course', $course)
            ->setParameter('date', $date)
            ->getQuery()->execute();
    }

    /** @return Assignment[] */
    public function findActiveByCustomerWithCourse(Customer $customer, \DateTime $today = null): array
    {
        $today ??= new \DateTime();

        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('a', 'c', 's', 'd', 'l', 'ag', 'p', 'z')
            ->join('a.course', 'c')
            ->join('c.school', 's')
            ->leftJoin('c.discipline', 'd')
            ->leftJoin('c.level', 'l')
            ->leftJoin('c.age', 'ag')
            ->leftJoin('c.place', 'p')
            ->leftJoin('p.zone', 'z')
            ->leftJoin('a.firstSession', 'fs')
            ->leftJoin('a.lastSession', 'ls')
            ->where('a.customer = :customer')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('fs'),
                $qb->expr()->lte('fs.day', ':date')
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('ls'),
                $qb->expr()->gte('ls.day', ':date')
            ))
            ->setParameter('customer', $customer)
            ->setParameter('date', $today)
            ->getQuery()->execute();
    }

    /** @return Assignment[] */
    public function findActiveByCourseWithCustomer(Course $course, \DateTime $today = null): array
    {
        $today ??= new \DateTime();

        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('a', 'c')
            ->join('a.customer', 'c')
            ->leftJoin('a.firstSession', 'fs')
            ->leftJoin('a.lastSession', 'ls')
            ->where('a.course = :course')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('fs'),
                $qb->expr()->lte('fs.day', ':date')
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('ls'),
                $qb->expr()->gte('ls.day', ':date')
            ))
            ->setParameter('course', $course)
            ->setParameter('date', $today)
            ->getQuery()->execute();
    }

    public function save(Assignment $assignment, bool $flush = true): void
    {
        $this->saveEntity($assignment, $flush);
    }

    public function remove(Assignment $assignment, bool $flush = true): void
    {
        $this->removeEntity($assignment, $flush);
    }
}
