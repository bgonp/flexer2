<?php

namespace App\DataFixtures;

use App\Repository\SchoolRepository;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SessionFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private SessionRepository $sessionRepository;

    private SessionService $sessionService;

    private SchoolRepository $schoolRepository;

    public function __construct(
        SessionRepository $sessionRepository,
        SessionService $sessionService,
        SchoolRepository $schoolRepository
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->sessionService = $sessionService;
        $this->schoolRepository = $schoolRepository;
    }

    public function load(ObjectManager $manager)
    {
        $schools = $this->schoolRepository->findAll();
        foreach ($schools as $school) {
            $courses = $school->getCourses();
            $seasons = $school->getSeasons();
            foreach ($courses as $course) {
                if ($course->isActive()) {
                    foreach ($seasons as $season) {
                        $this->sessionService->createSessions($season, $course, false);
                    }
                }
            }
        }
        $this->sessionRepository->flush();
    }

    public function getDependencies()
    {
        return [
            SchoolFixtures::class,
            CourseFixtures::class,
            SeasonFixtures::class,
            PeriodFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['until_sessions'];
    }
}
