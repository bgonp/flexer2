<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Persistence\ManagerRegistry;

class AssignmentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function save(Assignment $assignment, bool $flush = true): void
    {
        $this->saveEntity($assignment, $flush);
    }
}
