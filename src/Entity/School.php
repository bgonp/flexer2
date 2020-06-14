<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class School extends Named
{
    private ?string $description = null;

    private ?string $url = null;

    /** @var Collection|Course[] */
    private Collection $courses;

    /** @var Collection|Season[] */
    private Collection $seasons;

    /** @var Collection|Rate[] */
    private Collection $rates;

    public function __construct()
    {
        parent::__construct();
        $this->courses = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->rates = new ArrayCollection();
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

    /** @return Collection|Course[] */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    /** @return Collection|Season[] */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    /** @return Collection|Rate[] */
    public function getRates(): Collection
    {
        return $this->rates;
    }
}
