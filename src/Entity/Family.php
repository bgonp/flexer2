<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Family extends Base
{
    private ?string $notes = null;

    /** @var Collection|Customer[] */
    private Collection $customers;

    public function __construct()
    {
        parent::__construct();
        $this->customers = new ArrayCollection();
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

    /** @return Collection|Customer[] */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        $this->customers->add($customer);
        $customer->setFamily($this);

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        $this->customers->removeElement($customer);
        $customer->setFamily(null);

        return $this;
    }
}
