<?php

namespace App\DataFixtures;

use App\Entity\Discipline;
use App\Repository\DisciplineRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DisciplineFixtures extends Fixture implements FixtureGroupInterface
{
    private DisciplineRepository $disciplineRepository;

    public function __construct(DisciplineRepository $disciplineRepository)
    {
        $this->disciplineRepository = $disciplineRepository;
    }

    public function load(ObjectManager $manager)
    {
        $names = ['Patinaje estándar', 'Freeskate', 'Slalom', 'Hockey', 'Artístico'];
        foreach ($names as $name) {
            $this->disciplineRepository->save((new Discipline())->setName($name));
        }
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
