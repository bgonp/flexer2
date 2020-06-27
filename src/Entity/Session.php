<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Session extends Base
{
    private Course $course;

    private ?Period $period = null;

    private ?\DateTime $day = null;

    private ?\DateTime $time = null;

    private ?int $duration = null;

    private ?Place $place = null;

    private ?Discipline $discipline = null;

    private ?Level $level = null;

    private ?Age $age = null;

    private bool $isCancelled = false;

    private bool $isRetrievalSession = false;

    private ?Session $retrievalSession = null;

    private ?Listing $listing = null;

    /** @var Collection|Session[] */
    private Collection $retrievalSessionOf;

    /** @var Collection|Attendance[] */
    private Collection $attendances;

    public function __construct()
    {
        parent::__construct();
        $this->retrievalSessionOf = new ArrayCollection();
        $this->attendances = new ArrayCollection();
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

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getDay(): ?\DateTime
    {
        return $this->day;
    }

    public function setDay(?\DateTime $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(?\DateTime $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getAge(): ?Age
    {
        return $this->age;
    }

    public function setAge(?Age $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    public function isRetrievalSession(): bool
    {
        return $this->isRetrievalSession;
    }

    public function setIsRetrievalSession(bool $isRetrievalSession): self
    {
        $this->isRetrievalSession = $isRetrievalSession;

        return $this;
    }

    public function setIsCancelled(bool $isCancelled): self
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    public function getRetrievalSession(): ?Session
    {
        return $this->retrievalSession;
    }

    public function setRetrievalSession(?Session $retrievalSession): self
    {
        $this->retrievalSession = $retrievalSession;

        return $this;
    }

    /** @return Collection|Session[] */
    public function getRetrievalSessionOf(): Collection
    {
        return $this->retrievalSessionOf;
    }

    /** @return Collection|Attendance[] */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }
}
