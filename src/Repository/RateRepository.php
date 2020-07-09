<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rate;
use App\Entity\School;
use Doctrine\Persistence\ManagerRegistry;

class RateRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    /** @return Rate[] */
    public function findBySchool(?School $school): array
    {
        $query = $this->createQueryBuilder('r')
            ->select('r', 's')
            ->join('r.school', 's')
            ->orderBy('s.name')
            ->addOrderBy('r.name');
        if ($school) {
            $query->where('r.school = :school')->setParameter('school', $school);
        }

        return $query->getQuery()->execute();
    }

    public function save(Rate $rate, bool $flush = true)
    {
        $this->saveEntity($rate, $flush);
    }

    public function remove(Rate $rate, bool $flush = true)
    {
        $this->removeEntity($rate, $flush);
    }
}
