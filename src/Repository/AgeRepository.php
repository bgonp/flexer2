<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Age;

class AgeRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Age::class;
    }

    /**
     * @return Age[]
     */
    public function findAll(): array
    {
        /** @var Age[] $ages */
        $ages = $this->findBy([], ['minAge' => 'ASC']);

        return $ages;
    }

    /**
     * @return Age[]
     */
    public function findCompatibleAges(int $userAge): array
    {
        return ($qb = $this->createQueryBuilder('a'))
            ->where($qb->expr()->orX(
                $qb->expr()->isNull('a.minAge'),
                $qb->expr()->lte('a.minAge', ':user_age')
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('a.maxAge'),
                $qb->expr()->gte('a.maxAge', ':user_age')
            ))
            ->setParameter('user_age', $userAge)
            ->getQuery()->execute();
    }

    public function save(Age $age, bool $flush = true): void
    {
        $this->saveEntity($age, $flush);
    }

    public function remove(Age $age, bool $flush = true): void
    {
        $this->removeEntity($age, $flush);
    }
}
