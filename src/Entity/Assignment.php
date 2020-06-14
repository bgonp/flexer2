<?php

declare(strict_types=1);

namespace App\Entity;

class Assignment extends Base
{
    private ?string $notes = null;

    private Customer $customer;

    private Course $course;

    private Position $position;

    private ?Session $firstSession = null;

    private ?Session $lastSession = null;

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getFirstSession(): ?Session
    {
        return $this->firstSession;
    }

    public function setFirstSession(?Session $firstSession): self
    {
        $this->firstSession = $firstSession;

        return $this;
    }

    public function getLastSession(): ?Session
    {
        return $this->lastSession;
    }

    public function setLastSession(?Session $lastSession): self
    {
        $this->lastSession = $lastSession;

        return $this;
    }
}
