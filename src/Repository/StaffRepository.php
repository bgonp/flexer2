<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Staff;
use App\Entity\StaffPosition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ManagerRegistry;

class StaffRepository extends BaseRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Staff::class);
        $this->connection = $connection;
    }

    public function markAsStaff(Customer $customer, $unmark = false): bool
    {
        try {
            $this->connection->update(
                'customer',
                ['staff' => $unmark ? 0 : 1],
                ['id' => $customer->getId()]
            );
        } catch (DBALException $e) {
            return false;
        }

        return true;
    }

    /** @return Staff[] */
    public function findByListingAndPeriod(Listing $listing, Period $period): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'a', 'se', 'p', 'po')
            ->join('s.attendances', 'a')
            ->join('a.session', 'se')
            ->join('a.payment', 'p')
            ->join('a.position', 'po')
            ->where('se.listing = :listing')
            ->andWhere('se.period = :period')
            ->andWhere('po INSTANCE OF :position_class')
            ->setParameter('listing', $listing)
            ->setParameter('period', $period)
            ->setParameter('position_class', $this->getClassQueryData(StaffPosition::class))
            ->orderBy('s.surname', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->addOrderBy('se.day', 'ASC')
            ->getQuery()->execute();
    }

    /** @return Staff[] */
    public function findAll(): array
    {
        /** @var Staff[] $staffs */
        $staffs = $this->findBy([], ['surname' => 'ASC', 'name' => 'ASC']);

        return $staffs;
    }

    public function save(Staff $staff, bool $flush = true): void
    {
        $this->saveEntity($staff, $flush);
    }
}
