<?php

namespace App\DataFixtures;

use App\Entity\Level;
use App\Repository\LevelRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LevelFixtures extends Fixture implements FixtureGroupInterface
{
    private LevelRepository $levelRepository;

    public function __construct(LevelRepository $levelRepository)
    {
        $this->levelRepository = $levelRepository;
    }

    public function load(ObjectManager $manager)
    {
        $names = ['Iniciación', 'Básico', 'Intermedio', 'Avanzado'];
        foreach ($names as $index => $name) {
            $this->levelRepository->save((new Level())
                ->setName($name)
                ->setDifficulty($index + 1)
            );
        }
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
