<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rate;

class RateRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Rate::class;
    }

    public function save(Rate $rate, bool $flush = true)
    {
        $this->saveEntity($rate, $flush);
    }
}
