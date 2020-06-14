<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Place;

class PlaceRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Place::class;
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
}
