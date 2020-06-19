<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Discipline;
use Doctrine\Persistence\ManagerRegistry;

class DisciplineRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    /** @return Discipline[] */
    public function findAll(): array
    {
        /** @var Discipline[] $disciplines */
        $disciplines = $this->findBy([], ['name' => 'ASC']);

        return $disciplines;
    }

    public function save(Discipline $discipline, bool $flush = true): void
    {
        $this->saveEntity($discipline, $flush);
    }
}
