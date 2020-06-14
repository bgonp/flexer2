<?php

namespace App\DataFixtures;

use App\Entity\Age;
use App\Repository\AgeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AgeFixtures extends Fixture implements FixtureGroupInterface
{
    private AgeRepository $ageRepository;

    public function __construct(AgeRepository $ageRepository)
    {
        $this->ageRepository = $ageRepository;
    }

    public function load(ObjectManager $manager)
    {
        $ages = ['Infantil' => [4, 10], 'Jóvenes' => [10, 16], 'Adultos' => [16, null]];
        foreach ($ages as $name => $limits) {
            $this->ageRepository->save(
                (new Age())
                    ->setName($name)
                    ->setMinAge($limits[0])
                    ->setMaxAge($limits[1])
            );
        }
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
