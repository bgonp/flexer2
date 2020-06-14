<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Zone extends Named
{
    private ?string $description = null;

    /** @var Collection|Place[] */
    private Collection $places;

    public function __construct()
    {
        parent::__construct();
        $this->places = new ArrayCollection();
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

    public function getPlaces(): Collection
    {
        return $this->places;
    }
}
