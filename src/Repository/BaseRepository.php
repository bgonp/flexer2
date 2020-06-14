<?php

namespace App\Repository;

use App\Exception\Common\CannotFlushPersistOperationsException;
use App\Exception\Common\ObjectOfClassCouldNotBeRemovedException;
use App\Exception\Common\ObjectOfClassCouldNotBeStoredException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    protected Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, $this->entityClass());
        $this->connection = $connection;
    }

    abstract protected static function entityClass();

    public function flush(): void
    {
        try {
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException | ORMException $e) {
            throw CannotFlushPersistOperationsException::create();
        }
    }

    protected function saveEntity($entity, bool $flush = true): void
    {
        try {
            $this->getEntityManager()->persist($entity);
        } catch (ORMException $e) {
            throw ObjectOfClassCouldNotBeStoredException::create($entity);
        }
        if ($flush) {
            $this->flush();
        }
    }

    protected function removeEntity($entity, bool $flush = true): void
    {
        try {
            $this->getEntityManager()->remove($entity);
        } catch (ORMException $e) {
            throw ObjectOfClassCouldNotBeRemovedException::create($entity);
        }
        if ($flush) {
            $this->flush();
        }
    }
}
