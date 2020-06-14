<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Staff;
use Doctrine\DBAL\DBALException;

class StaffRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Staff::class;
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
