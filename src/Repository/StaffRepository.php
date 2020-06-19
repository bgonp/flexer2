<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Staff;
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
