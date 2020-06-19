<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Level;
use Doctrine\Persistence\ManagerRegistry;

class LevelRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
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
