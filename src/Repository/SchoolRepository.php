<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\School;
use Doctrine\Persistence\ManagerRegistry;

class SchoolRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    /** @return School[] */
    public function findAll(): array
    {
        /** @var School[] $schools */
        $schools = $this->findBy([], ['name' => 'ASC']);

        return $schools;
    }

    public function save(School $school, bool $flush = true): void
    {
        $this->saveEntity($school, $flush);
    }
}
