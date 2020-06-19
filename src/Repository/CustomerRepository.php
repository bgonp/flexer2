<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Listing;
use App\Entity\Period;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends PaginableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /** @return Customer[] */
    public function findBySearchTerm(string $search, int $page = null): array
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.name LIKE :search')
            ->orWhere('c.surname LIKE :search')
            ->orWhere('c.email LIKE :search')
            ->orWhere('c.phone LIKE :search')
            ->setParameter('search', "%$search%");
        if ($page) {
            $query
                ->setFirstResult($this->getOffset($page))
                ->setMaxResults(self::PER_PAGE);
        }

        return $query->getQuery()->execute();
    }

    /** @return Customer[] */
    public function findByFamiliar(Customer $customer): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.family = :family')
            ->setParameter('family', $customer->getFamily())
            ->getQuery()->execute();
    }

    public function getLastPageBySearchTerm(string $search): int
    {
        $count = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->where('c.name LIKE :search')
            ->orWhere('c.surname LIKE :search')
            ->orWhere('c.email LIKE :search')
            ->orWhere('c.phone LIKE :search')
            ->setParameter('search', "%$search%")
            ->getQuery()->getSingleScalarResult();

        return (int) ceil($count / self::PER_PAGE) ?: 1;
    }

    /** @return Customer[] */
    public function findAll(int $page = 1): array
    {
        /** @var Customer[] $customers */
        $customers = $this->findBy(
            [],
            ['surname' => 'ASC', 'name' => 'ASC'],
            self::PER_PAGE,
            $this->getOffset($page)
        );

        return $customers;
    }

    /** @return Customer[] */
    public function findByListingAndPeriod(Listing $listing, Period $period): array
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
    }

    public function save(Customer $customer, bool $flush = true): void
    {
        $this->saveEntity($customer, $flush);
    }

    public function remove(Customer $customer, bool $flush = true): void
    {
        $this->removeEntity($customer, $flush);
    }
}
