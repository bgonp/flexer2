<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Season extends Base
{
    private ?\DateTime $initDate = null;

    private ?\DateTime $finishDate = null;

    private School $school;

    /** @var Collection|Period[] */
    private Collection $periods;

    public function __construct()
    {
        parent::__construct();
        $this->periods = new ArrayCollection();
    }

    public function getInitDate(): ?\DateTime
    {
        return $this->initDate;
    }

    public function setInitDate(?\DateTime $initDate): self
    {
        $this->initDate = $initDate;

        return $this;
    }

    public function getFinishDate(): ?\DateTime
    {
        return $this->finishDate;
    }

    public function setFinishDate(?\DateTime $finishDate): self
    {
        $this->finishDate = $finishDate;

        return $this;
    }

    public function getSchool(): School
    {
        return $this->school;
    }

    public function setSchool(School $school): self
    {
        $this->school = $school;

        return $this;
    }

    /** @return Collection|Period[] */
    public function getPeriods(): Collection
    {
        return $this->periods;
    }

    public function addPeriod(Period $period): self
    {
        $this->periods->add($period);
        $period->setSeason($this);

        return $this;
    }
}
