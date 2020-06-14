<?php

namespace App\DataFixtures;

use App\Entity\Position;
use App\Repository\PositionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PositionFixtures extends Fixture implements FixtureGroupInterface
{
    private PositionRepository $positionRepository;

    public function __construct(PositionRepository $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function load(ObjectManager $manager)
    {
        $this->positionRepository->save((new Position())->setName('Monitor')->setIsStaff(true));
        $this->positionRepository->save((new Position())->setName('Apoyo')->setIsStaff(true));
        $this->positionRepository->save((new Position())->setName('Alumno'));
        $this->positionRepository->save((new Position())->setName('Becado'));
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
