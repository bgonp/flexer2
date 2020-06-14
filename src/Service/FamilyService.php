<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Family;
use App\Exception\Family\CustomerAlreadyHasFamilyException;
use App\Repository\CustomerRepository;
use App\Repository\FamilyRepository;

class FamilyService
{
    private FamilyRepository $familyRepository;

    private CustomerRepository $customerRepository;

    public function __construct(FamilyRepository $familyRepository, CustomerRepository $customerRepository)
    {
        $this->familyRepository = $familyRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Customer[] $customers
     *
     * @return Family
     * @throws CustomerAlreadyHasFamilyException
     */
    public function createFamilyFromCustomers(array $customers): Family
    {
        $family = new Family();

        foreach ($customers as $customer) {
            if ($customer->getFamily()) {
                throw CustomerAlreadyHasFamilyException::create($customer);
            }

            $this->customerRepository->save($customer->setFamily($family), false);
        }

        $this->familyRepository->save($family);

        return $family;
    }

    public function subtractCustomerFromFamily(Customer $customer): void
    {
        if (!$family = $customer->getFamily()) {
            return;
        }

        if ($user = $customer->getUser()) {
            if (count($customers = $user->getCustomers()) > 1) {
                $customer->setUser(null);
            }
        }

        $this->customerRepository->save($customer->setFamily(null));
    }
}
