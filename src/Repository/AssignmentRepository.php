<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Assignment;

class AssignmentRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Assignment::class;
    }

    public function save(Assignment $assignment, bool $flush = true): void
    {
        $this->saveEntity($assignment, $flush);
    }
}
