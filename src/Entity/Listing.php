<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

// TODO: Orphan removal
class Listing extends Base
{
    /** @var Collection|Course[] */
    private Collection $courses;

    /** @var Collection|Session[] */
    private Collection $sessions;

    public function __construct()
    {
        parent::__construct();
        $this->courses = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    /** @return Course[]|Collection */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setListing($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->setListing(new self());
        }

        return $this;
    }

    /** @return Session[]|Collection */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }
}
