<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Zone;
use Doctrine\Persistence\ManagerRegistry;

class ZoneRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
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

    public function remove(Zone $zone, bool $flush = true): void
    {
        $this->removeEntity($zone, $flush);
    }
}
