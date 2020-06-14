<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\FamilyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CustomerFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private CustomerRepository $customerRepository;

    private FamilyRepository $familyRepository;

    public function __construct(CustomerRepository $customerRepository, FamilyRepository $familyRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->familyRepository = $familyRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('es_ES');
        $families = $this->familyRepository->findAll();
        $maxBirthdate = (new \DateTime())->setDate(2015, 12, 31);
        for ($i = 0; $i < 100; ++$i) {
            $customer = (new Customer())
                ->setName($faker->firstName)
                ->setSurname($faker->lastName.' '.$faker->lastName)
                ->setBirthdate($faker->dateTimeThisCentury($maxBirthdate))
                ->setEmail($faker->email);
            if ($i < 20) {
                $customer->setFamily($families[$i % count($families)]);
            }
            $this->customerRepository->save($customer);
        }
    }

    public function getDependencies()
    {
        return [FamilyFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
