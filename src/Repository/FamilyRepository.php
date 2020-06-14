<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Family;

class FamilyRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Family::class;
    }

    public function save(Family $family, bool $flush = true): void
    {
        $this->saveEntity($family, $flush);
    }
}
