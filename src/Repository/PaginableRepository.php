<?php

declare(strict_types=1);

namespace App\Repository;

abstract class PaginableRepository extends BaseRepository
{
    protected const PER_PAGE = 20;

    protected ?int $lastPage = null;

    public function getLastPage(): int
    {
        if (is_null($this->lastPage)) {
            $this->lastPage = (int) ceil($this->count([]) / self::PER_PAGE);
        }

        return $this->lastPage;
    }

    protected function getOffset(int $page): int
    {
        return ($page - 1) * self::PER_PAGE;
    }
}
