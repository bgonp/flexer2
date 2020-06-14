<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    /** @return Session[]|Collection */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }
}