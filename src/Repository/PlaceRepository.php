<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Persistence\ManagerRegistry;

class PlaceRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    /**
     * @return Place[]
     */
    public function findAll(): array
    {
        /** @var Place[] $places */
        $places = $this->findBy([], ['name' => 'ASC']);

        return $places;
    }

    public function save(Place $place, bool $flush = true): void
    {
        $this->saveEntity($place, $flush);
    }

    public function remove(Place $place, bool $flush = true): void
    {
        $this->removeEntity($place, $flush);
    }
}
