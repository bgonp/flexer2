<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Zone;

class ZoneRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Zone::class;
    }

    /** @return Zone[] */
    public function findAll(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function save(Zone $zone, bool $flush = true): void
    {
        $this->saveEntity($zone, $flush);
    }
}
