<?php

namespace App\DataFixtures;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use App\Repository\ZoneRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private PlaceRepository $placeRepository;
    private ZoneRepository $zoneRepository;

    public function __construct(PlaceRepository $placeRepository, ZoneRepository $zoneRepository)
    {
        $this->placeRepository = $placeRepository;
        $this->zoneRepository = $zoneRepository;
    }

    public function load(ObjectManager $manager)
    {
        $names = ['Paseo de Coches', 'Ángel Caído', 'Juan Carlos I', 'Nuevos Ministerios', 'Brezo Osuna'];
        $zones = $this->zoneRepository->findAll();
        foreach ($names as $index => $name) {
            $zone = $zones[$index <= 1 ? 0 : 1];
            $this->placeRepository->save((new Place())
                ->setName($name)
                ->setZone($zone)
            );
        }
    }

    public function getDependencies()
    {
        return [ZoneFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
