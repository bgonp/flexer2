<?php

namespace App\Repository;

use App\Entity\User;

class UserRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return User::class;
    }

    public function findOneById(string $id): ?User
    {
        /** @var User $user */
        $user = $this->find($id);

        return $user;
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
