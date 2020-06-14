<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Age extends Named
{
    private ?string $description = null;

    private ?string $url = null;

    private ?int $minAge = null;

    private ?int $maxAge = null;

    private Collection $courses;

    private Collection $sessions;

    public function __construct()
    {
        parent::__construct();
        $this->courses = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(?int $minAge): self
    {
        $this->minAge = $minAge;

        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): self
    {
        $this->maxAge = $maxAge;

        return $this;
    }

    /** @return Collection|Course[] */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    /** @return Collection|Session[] */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }
}
