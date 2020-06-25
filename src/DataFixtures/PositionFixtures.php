<?php

namespace App\DataFixtures;

use App\Entity\CustomerPosition;
use App\Entity\StaffPosition;
use App\Repository\CustomerPositionRepository;
use App\Repository\StaffPositionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PositionFixtures extends Fixture implements FixtureGroupInterface
{
    private CustomerPositionRepository $customerPositionRepository;

    private StaffPositionRepository $staffPositionRepository;

    public function __construct(
        CustomerPositionRepository $customerPositionRepository,
        StaffPositionRepository $staffPositionRepository
    ) {
        $this->customerPositionRepository = $customerPositionRepository;
        $this->staffPositionRepository = $staffPositionRepository;
    }

    public function load(ObjectManager $manager)
    {
        $this->staffPositionRepository->save((new StaffPosition())->setName('Monitor'));
        $this->staffPositionRepository->save((new StaffPosition())->setName('Apoyo'));
        $this->customerPositionRepository->save((new CustomerPosition())->setName('Alumno'));
        $this->customerPositionRepository->save((new CustomerPosition())->setName('Becado'));
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
