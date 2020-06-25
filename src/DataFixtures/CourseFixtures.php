<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Listing;
use App\Repository\AgeRepository;
use App\Repository\CourseRepository;
use App\Repository\DisciplineRepository;
use App\Repository\LevelRepository;
use App\Repository\ListingRepository;
use App\Repository\PlaceRepository;
use App\Repository\SchoolRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private CourseRepository $courseRepository;

    private SchoolRepository $schoolRepository;

    private AgeRepository $ageRepository;

    private DisciplineRepository $disciplineRepository;

    private LevelRepository $levelRepository;

    private PlaceRepository $placeRepository;

    private ListingRepository $listingRepository;

    public function __construct(
        CourseRepository $courseRepository,
        SchoolRepository $schoolRepository,
        AgeRepository $ageRepository,
        DisciplineRepository $disciplineRepository,
        LevelRepository $levelRepository,
        PlaceRepository $placeRepository,
        ListingRepository $listingRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->schoolRepository = $schoolRepository;
        $this->ageRepository = $ageRepository;
        $this->disciplineRepository = $disciplineRepository;
        $this->levelRepository = $levelRepository;
        $this->placeRepository = $placeRepository;
        $this->listingRepository = $listingRepository;
    }

    public function load(ObjectManager $manager)
    {
        $school = $this->schoolRepository->findAll()[0];
        $ages = $this->ageRepository->findAll();
        $disciplines = $this->disciplineRepository->findAll();
        $levels = $this->levelRepository->findAll();
        $places = $this->placeRepository->findAll();
        for ($i = 0; $i < 15; ++$i) {
            $this->listingRepository->save($listing = new Listing(), false);
            $course = (new Course())
                ->setSchool($school)
                ->setPlace($places[rand(0, count($places) - 1)])
                ->setDiscipline($disciplines[rand(0, count($disciplines) - 1)])
                ->setLevel($levels[rand(0, count($levels) - 1)])
                ->setAge($ages[rand(0, count($ages) - 1)])
                ->setDayOfWeek([rand(1, 7)])
                ->setTime((new \DateTime())->setTime(rand(10, 20), rand(0, 1) ? 0 : 30))
                ->setDuration(60)
                ->setListing($listing);
            if (2 == $i) {
                $course->setWeekOfMonth([-1]);
            }
            if ($i < 2) {
                $course->setIsActive(false);
            }
            $this->courseRepository->save($course);
        }
    }

    public function getDependencies()
    {
        return [
            SchoolFixtures::class,
            AgeFixtures::class,
            DisciplineFixtures::class,
            LevelFixtures::class,
            PlaceFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
