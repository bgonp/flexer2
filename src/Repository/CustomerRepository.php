<?php

declare(strict_types=1);

namespace App\Repository;

use App\DQL\PaginableQuery;
use App\Entity\Customer;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /** @return Customer[] */
    public function findBySearchTerm(string $search): array
    {
        $query = $this->addSearchTerms($search, $this->getMainQuery());

        return $query->getQuery()->execute();
    }

    public function findBySearchTermPaged(string $search, int $page = 1): PaginableQuery
    {
        $query = $this->addSearchTerms($search, $this->getMainQuery());

        return new PaginableQuery($query->getQuery(), $page);
    }

    public function findAllPaged(int $page = 1): PaginableQuery
    {
        return $this->findBySearchTermPaged('', $page);
    }

    /** @return Customer[] */
    public function findByFamiliar(Customer $customer): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.family = :family')
            ->setParameter('family', $customer->getFamily())
            ->getQuery()->execute();
    }

    // TODO: Ver si al final hace falta
    /*public function findByListingAndPeriod(Listing $listing, Period $period): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'a', 's', 'p', 'o')
            ->join('c.attendances', 'a')
            ->join('a.session', 's')
            ->join('a.payment', 'p')
            ->join('a.position', 'o')
            ->where('s.listing = :listing')
            ->andWhere('s.period = :period')
            ->setParameter('listing', $listing)
            ->setParameter('period', $period)
            ->orderBy('c.surname')
            ->addOrderBy('c.name')
            ->addOrderBy('s.day')
            ->getQuery()->execute();
    }*/

    public function save(Customer $customer, bool $flush = true): void
    {
        $this->saveEntity($customer, $flush);
    }

    public function remove(Customer $customer, bool $flush = true): void
    {
        $this->removeEntity($customer, $flush);
    }

    private function getMainQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'f', 'fc')
            ->leftJoin('c.family', 'f')
            ->leftJoin('f.customers', 'fc')
            ->orderBy('c.surname', 'ASC')
            ->addOrderBy('c.name', 'ASC');
    }

    private function addSearchTerms(string $search, QueryBuilder $query): QueryBuilder
    {
        $terms = explode(' ', trim($search));
        foreach ($terms as $index => $term) {
            $query
                ->andWhere("SEARCH_STRING() LIKE :search$index")
                ->setParameter("search$index", "%$term%");
        }

        return $query;
    }
}
