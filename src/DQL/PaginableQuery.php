<?php

declare(strict_types=1);

namespace App\DQL;

use App\Exception\Common\PageOutOfBoundsException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginableQuery
{
    const PER_PAGE = 20;

    private int $page;

    private int $lastPage;

    private iterable $results;

    /** @throws PageOutOfBoundsException */
    public function __construct(Query $query, int $page = 1)
    {
        $this->page = $page;

        $this->results = new Paginator($query
            ->setFirstResult(($this->page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE));

        $this->lastPage = (int) ceil($this->results->count() / self::PER_PAGE) ?: 1;

        if (1 > $this->page || $this->page > $this->lastPage) {
            throw PageOutOfBoundsException::create();
        }
    }

    public function getResults(): iterable
    {
        return $this->results;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }
}