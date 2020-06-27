<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Entity\User;
use App\Exception\Customer\CustomerAlreadyHasUserException;
use App\Exception\Customer\CustomerRequiresEmailException;
use App\Exception\User\UserAlreadyExistsException;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserService
{
    private UserRepository $userRepository;

    private CustomerRepository $customerRepository;

    public function __construct(UserRepository $userRepository, CustomerRepository $customerRepository)
    {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    public function createUserFromCustomer(Customer $customer, string $password): void
    {
        if (!$email = $customer->getEmail()) {
            throw CustomerRequiresEmailException::create($customer);
        }

        $user = (new User())->setEmail($customer->getEmail())->setPassword($password);
        try {
            $this->userRepository->save($user);
        } catch (UniqueConstraintViolationException $e) {
            throw UserAlreadyExistsException::create($customer->getEmail());
        }

        if ($family = $customer->getFamily()) {
            $familiars = $family->getCustomers();
        } else {
            $familiars = [$customer];
        }

        foreach ($familiars as $familiar) {
            if ($familiar->getUser()) {
                $this->userRepository->remove($user); // TODO: Es necesario?
                throw CustomerAlreadyHasUserException::create($customer);
            }

            $familiar->setUser($user);
            $this->customerRepository->save($familiar, false);
        }
        $this->customerRepository->flush();
    }
}
