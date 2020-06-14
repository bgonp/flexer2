<?php

declare(strict_types=1);

namespace App\Entity;

class Rate extends Named
{
    private float $amount;

    private bool $available = true;

    private bool $spreadable = true;

    private int $customersCount = 1;

    private int $periodsCount = 1;

    private int $coursesCount = 1;

    private School $school;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function isSpreadable(): bool
    {
        return $this->spreadable;
    }

    public function setSpreadable(bool $spreadable): self
    {
        $this->spreadable = $spreadable;

        return $this;
    }

    public function getCustomersCount(): int
    {
        return $this->customersCount;
    }

    public function setCustomersCount(int $customersCount): self
    {
        $this->customersCount = $customersCount;

        return $this;
    }

    public function getPeriodsCount(): int
    {
        return $this->periodsCount;
    }

    public function setPeriodsCount(int $periodsCount): self
    {
        $this->periodsCount = $periodsCount;

        return $this;
    }

    public function getCoursesCount(): int
    {
        return $this->coursesCount;
    }

    public function setCoursesCount(int $coursesCount): self
    {
        $this->coursesCount = $coursesCount;

        return $this;
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
}
