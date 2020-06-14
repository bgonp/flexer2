<?php

namespace App\DataFixtures;

use App\Entity\Zone;
use App\Repository\ZoneRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ZoneFixtures extends Fixture implements FixtureGroupInterface
{
    private ZoneRepository $zoneRepository;

    public function __construct(ZoneRepository $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    public function load(ObjectManager $manager)
    {
        $names = ['El Retiro', 'Otros'];
        foreach ($names as $name) {
            $this->zoneRepository->save((new Zone())->setName($name));
        }
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
