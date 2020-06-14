<?php

namespace App\DataFixtures;

use App\Entity\School;
use App\Repository\SchoolRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SchoolFixtures extends Fixture implements FixtureGroupInterface
{
    private SchoolRepository $schoolRepository;

    public function __construct(SchoolRepository $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function load(ObjectManager $manager)
    {
        $names = ['Escuela Retiro', 'Otras escuelas'];
        foreach ($names as $name) {
            $this->schoolRepository->save((new School())->setName($name));
        }
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
