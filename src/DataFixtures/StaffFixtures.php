<?php

namespace App\DataFixtures;

use App\Repository\CustomerRepository;
use App\Repository\StaffRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StaffFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private StaffRepository $staffRepository;

    private CustomerRepository $customerRepository;

    public function __construct(StaffRepository $staffRepository, CustomerRepository $customerRepository)
    {
        $this->staffRepository = $staffRepository;
        $this->customerRepository = $customerRepository;
    }

    public function load(ObjectManager $manager)
    {
        $customers = $this->customerRepository->findAll();
        for ($i = 1; $i <= 6; ++$i) {
            $this->staffRepository->markAsStaff($customers[count($customers) - $i]);
        }
    }

    public function getDependencies()
    {
        return [CustomerFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
