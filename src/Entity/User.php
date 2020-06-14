<?php

namespace App\Entity;

use App\Security\Role;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends Base implements UserInterface
{
    protected string $email;

    protected string $password;

    protected array $roles;

    /** @var Collection|Customer[] */
    protected Collection $customers;

    public function __construct()
    {
        parent::__construct();
        $this->roles[] = Role::ROLE_USER;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    /** @return Collection|Customer[] */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }
}
