<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Payment extends Base
{
    private Rate $rate;

    private float $amount = 0;

    private bool $isTransfer = false;

    private ?string $document = null;

    private ?string $notes = null;

    /** @var Collection|Attendance[] */
    private Collection $attendances;

    public function __construct()
    {
        parent::__construct();
        $this->attendances = new ArrayCollection();
    }

    public function getRate(): Rate
    {
        return $this->rate;
    }

    public function setRate(Rate $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function isTransfer(): bool
    {
        return $this->isTransfer;
    }

    public function setIsTransfer(bool $isTransfer): self
    {
        $this->isTransfer = $isTransfer;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /** @return Collection|Attendance[] */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }
}
