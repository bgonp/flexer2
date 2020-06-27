<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\StaffRepository;
use App\Repository\UserRepository;
use App\Security\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private UserRepository $userRepository;

    private StaffRepository $staffRepository;

    private CustomerRepository $customerRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        StaffRepository $staffRepository,
        CustomerRepository $customerRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->staffRepository = $staffRepository;
        $this->customerRepository = $customerRepository;
    }

    public function load(ObjectManager $manager)
    {
        $staffs = $this->staffRepository->findAll();
        foreach ($staffs as $index => $staff) {
            $user = (new User())
                ->setEmail(0 === $index ? 'admin@admin.com' : $staff->getEmail())
                ->setRoles([Role::ROLE_USER, Role::ROLE_ADMIN]);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
            $this->userRepository->save($user, false);
            $this->staffRepository->save($staff->setUser($user));
        }
        // TODO
        /*$customers = $this->customerRepository->findAll();
        foreach ($customers as $customer) {
            if (!rand(0, 10)) {
                $user = (new User())->setEmail($customer->getEmail());
                $user->setRoles([Role::ROLE_USER, Role::ROLE_ADMIN]);
                $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
                $this->userRepository->save($user);
                $this->customerRepository->save($customer->setUser($user));
            }
        }*/
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class,
            StaffFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
