<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Level;

class LevelRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Level::class;
    }

    /** @return Level[] */
    public function findAll(): array
    {
        /** @var Level[] $levels */
        $levels = $this->findBy([], ['difficulty' => 'ASC']);

        return $levels;
    }

    public function save(Level $level, bool $flush = true): void
    {
        $this->saveEntity($level, $flush);
    }
}
