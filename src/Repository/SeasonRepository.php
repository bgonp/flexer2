<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Season;

class SeasonRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Season::class;
    }

    public function save(Season $season, bool $flush = true): void
    {
        $this->saveEntity($season, $flush);
    }
}
