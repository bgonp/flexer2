<?php

namespace App\DataFixtures;

use App\Entity\Season;
use App\Repository\SchoolRepository;
use App\Repository\SeasonRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private SeasonRepository $seasonRepository;

    private SchoolRepository $schoolRepository;

    public function __construct(SeasonRepository $seasonRepository, SchoolRepository $schoolRepository)
    {
        $this->seasonRepository = $seasonRepository;
        $this->schoolRepository = $schoolRepository;
    }

    public function load(ObjectManager $manager)
    {
        $schools = $this->schoolRepository->findAll();
        $intervals = [
            [new \DateTime('2017-09-01'), new \DateTime('2018-06-30')],
            [new \DateTime('2018-09-01'), new \DateTime('2019-06-30')],
            [new \DateTime('2019-09-01'), new \DateTime('2020-06-30')],
        ];
        foreach ($schools as $school) {
            foreach ($intervals as $interval) {
                $this->seasonRepository->save((new Season())
                    ->setSchool($school)
                    ->setInitDate($interval[0])
                    ->setFinishDate($interval[1])
                );
            }
        }
    }

    public function getDependencies()
    {
        return [SchoolFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
