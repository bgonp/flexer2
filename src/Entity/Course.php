<?php

declare(strict_types=1);

namespace App\Entity;

use App\Utils\CourseName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Course extends Base implements \JsonSerializable
{
    private School $school;

    private bool $isActive = true;

    private ?\DateTime $initDate = null;

    private ?\DateTime $finishDate = null;

    private ?Place $place = null;

    private ?Discipline $discipline = null;

    private ?Level $level = null;

    private ?Age $age = null;

    private ?array $weekOfMonth = null;

    private ?array $dayOfWeek = null;

    private ?\DateTime $time = null;

    private ?int $duration = null;

    private Listing $listing;

    /** @var Collection|Session[] */
    private Collection $sessions;

    /** @var Collection|Assignment[] */
    private Collection $assignments;

    public function __construct()
    {
        parent::__construct();
        $this->sessions = new ArrayCollection();
        $this->assignments = new ArrayCollection();
        $this->listing = new Listing();
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    public function getWeekOfMonth(): ?array
    {
        return $this->weekOfMonth;
    }

    public function setWeekOfMonth(?array $weekOfMonth): self
    {
        $this->weekOfMonth = $weekOfMonth;

        return $this;
    }

    public function getDayOfWeek(): ?array
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(?array $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

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

    /** @return Collection|Session[] */
    public function getSessions()
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        $this->sessions->add($session);
        $session->setCourse($this);

        return $this;
    }

    public function removeSession(Session $session): self
    {
        $this->sessions->removeElement($session);
        $session->setCourse(null);

        return $this;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }

    /** @return Collection|Assignment[] */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function JsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => CourseName::get($this),
            'active' => $this->isActive,
            'initDate' => $this->initDate,
            'finishDate' => $this->finishDate,
            'time' => $this->time,
            'duration' => $this->duration,
            'listing' => $this->listing->getId(),
            'school' => ['id' => $this->school->getId(), 'name' => $this->school->getName()],
            'place' => ['id' => $this->place->getId(), 'name' => $this->place->getName()],
            'discipline' => ['id' => $this->discipline->getId(), 'name' => $this->discipline->getName()],
            'level' => ['id' => $this->level->getId(), 'name' => $this->level->getName()],
            'age' => ['id' => $this->age->getId(), 'name' => $this->age->getName()],
        ];
    }
}
