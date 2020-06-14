<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Period extends Named
{
    private Season $season;

    private ?\DateTime $initDate = null;

    private ?\DateTime $finishDate = null;

    /** @var int[] */
    private array $holidays = [];

    /** @var Collection|Session[] */
    private Collection $sessions;

    public function __construct()
    {
        parent::__construct();
        $this->sessions = new ArrayCollection();
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
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

    public function getHolidays(): array
    {
        return $this->holidays;
    }

    public function isHoliday(\DateTime $date): bool
    {
        $timestamp = $date->setTime(0, 0, 0)->getTimestamp();

        return in_array($timestamp, $this->holidays, true);
    }

    public function addHoliday(\DateTime $holiday): self
    {
        $timestamp = $holiday->setTime(0, 0, 0)->getTimestamp();
        for ($i = 0; $i < count($this->holidays); ++$i) {
            if ($this->holidays[$i] === $timestamp) {
                return $this;
            } elseif ($timestamp > $this->holidays[$i]) {
                break;
            }
        }
        array_splice($this->holidays, $i, 0, $timestamp);

        return $this;
    }

    public function removeHoliday(\DateTime $holiday): self
    {
        $timestamp = $holiday->setTime(0, 0, 0)->getTimestamp();
        $index = array_search($timestamp, $this->holidays);
        if (false !== $index) {
            unset($this->holidays[$index]);
            $this->holidays = array_values($this->holidays);
        }

        return $this;
    }

    /** @return Collection|Session[] */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }
}
