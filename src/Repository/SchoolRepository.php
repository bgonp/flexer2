<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\School;

class SchoolRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return School::class;
    }

    /** @return School[] */
    public function findAll(): array
    {
        /** @var School[] $schools */
        $schools = $this->findBy([], ['name' => 'ASC']);

        return $schools;
    }

    public function save(School $school, bool $flush = true): void
    {
        $this->saveEntity($school, $flush);
    }
}
