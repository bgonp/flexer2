<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByEmail(string $email): ?User
    {
        /** @var User $user */
        $user = $this->findOneBy(['email' => $email]);

        return $user;
    }

    public function save(User $user, bool $flush = true): void
    {
        $this->saveEntity($user, $flush);
    }

    public function remove(User $user, bool $flush = true): void
    {
        $this->removeEntity($user, $flush);
    }
}
