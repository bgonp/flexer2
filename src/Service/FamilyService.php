<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Family;
use App\Exception\Family\CustomerAlreadyHasFamilyException;
use App\Exception\Family\CustomersAlreadyHasTheirOwnFamilyException;
use App\Repository\FamilyRepository;

class FamilyService
{
    private FamilyRepository $familyRepository;

    public function __construct(FamilyRepository $familyRepository)
    {
        $this->familyRepository = $familyRepository;
    }

    /**
     * @throws CustomerAlreadyHasFamilyException|CustomersAlreadyHasTheirOwnFamilyException
     */
    public function setCustomersAsFamily(Customer $customer, Customer $familiar): Family
    {
        if ($customer->getFamily() && $customer->getFamily()->equals($familiar->getFamily())) {
            return $customer->getFamily();
        }

        if ($customer->getFamily() && $familiar->getFamily()) {
            throw CustomersAlreadyHasTheirOwnFamilyException::create($customer, $familiar);
        }

        if (!$customer->getFamily() && !$familiar->getFamily()) {
            return $this->createFamilyFromCustomers([$customer, $familiar]);
        }

        if ($family = $customer->getFamily()) {
            $this->familyRepository->save($family->addCustomer($familiar));
        } elseif ($family = $familiar->getFamily()) {
            $this->familyRepository->save($family->addCustomer($customer));
        }

        return $family;
    }

    /**
     * @param Customer[] $customers
     *
     * @return Family|null
     * @throws CustomerAlreadyHasFamilyException
     */
    public function createFamilyFromCustomers(array $customers): ?Family
    {
        if (1 >= count($customers)) {
            return null;
        }

        $family = new Family();

        foreach ($customers as $customer) {
            if ($customer->getFamily()) {
                throw CustomerAlreadyHasFamilyException::create($customer);
            }
            $family->addCustomer($customer);
        }

        $this->familyRepository->save($family);

        return $family;
    }

    public function subtractCustomerFromFamily(Customer $customer): ?Family
    {
        if (!$family = $customer->getFamily()) {
            return null;
        }

        if (($user = $customer->getUser()) && count($customers = $user->getCustomers()) > 1) {
            $customer->setUser(null);
        }
        $family->removeCustomer($customer);
        $this->familyRepository->save($family);

        if (1 === $family->getCustomers()->count()) {
            $family->removeCustomer($family->getCustomers()->getValues()[0]);
            $this->familyRepository->remove($family);

            return null;
        }

        return $family;
    }
}
