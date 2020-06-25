<?php

namespace App\Repository;

use App\Entity\Base;
use App\Exception\Common\CannotFlushPersistOperationsException;
use App\Exception\Common\ObjectOfClassCouldNotBeRemovedException;
use App\Exception\Common\ObjectOfClassCouldNotBeStoredException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function flush(): void
    {
        try {
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException | ORMException $e) {
            throw CannotFlushPersistOperationsException::create();
        }
    }

    protected function saveEntity(Base $entity, bool $flush = true): void
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

    protected function removeEntity(Base $entity, bool $flush = true): void
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

    protected function getClassQueryData(string $className): ClassMetadata
    {
        return $this->getEntityManager()->getClassMetadata($className);
    }
}
