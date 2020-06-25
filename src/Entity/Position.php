<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class Position extends Named
{
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
