<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Position extends Named
{
    private bool $isStaff = false;

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

    public function isStaff(): bool
    {
        return $this->isStaff;
    }

    public function setIsStaff(bool $isStaff): self
    {
        $this->isStaff = $isStaff;

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
}
