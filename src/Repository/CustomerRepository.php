<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Listing;
use App\Entity\Period;

class CustomerRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Customer::class;
    }

    /** @return Customer[] */
    public function findAll(): array
    {
        /** @var Customer[] $customers */
        $customers = $this->findBy([], ['surname' => 'ASC', 'name' => 'ASC']);

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
}
