<?php

declare(strict_types=1);

namespace App\Entity;

class Attendance extends Base
{
    private Customer $customer;

    private Session $session;

    private Position $position;

    private bool $isUsed = false;

    private bool $willBeUsed = true;

    private ?string $notes = null;

    private ?Payment $payment = null;

    private bool $isPaidHere = false;

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function setSession(Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isUsed): self
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    public function willBeUsed(): bool
    {
        return $this->willBeUsed;
    }

    public function setWillBeUsed(bool $willBeUsed): self
    {
        $this->willBeUsed = $willBeUsed;

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

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function isPaidHere(): bool
    {
        return $this->isPaidHere;
    }

    public function setIsPaidHere(bool $isPaidHere): self
    {
        $this->isPaidHere = $isPaidHere;

        return $this;
    }
}
