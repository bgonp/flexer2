<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Customer extends Named implements \JsonSerializable
{
    private ?string $surname = null;

    private ?\DateTime $birthdate = null;

    private ?string $email = null;

    private ?string $phone = null;

    private ?string $notes = null;

    private ?User $user = null;

    private ?Family $family = null;

    /** @var Collection|Assignment[] */
    private Collection $assignments;

    /** @var Collection|Attendance[] */
    private Collection $attendances;

    public function __construct()
    {
        parent::__construct();
        $this->assignments = new ArrayCollection();
        $this->attendances = new ArrayCollection();
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTime $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): self
    {
        $this->family = $family;

        return $this;
    }

    /** @return Collection|Assignment[] */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    /** @return Collection|Attendance[] */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }

    public function jsonSerialize()
    {
        $json = [];
        $fields = ['id', 'name', 'surname', 'birthdate', 'email', 'phone'];
        foreach ($fields as $field) {
            $json[$field] = $this->{$field};
        }

        return $json;
    }
}
