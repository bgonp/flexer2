<?php

namespace App\DataFixtures;

use App\Entity\Period;
use App\Repository\PeriodRepository;
use App\Repository\SchoolRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PeriodFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private PeriodRepository $periodRepository;

    private SchoolRepository $schoolRepository;

    public function __construct(PeriodRepository $periodRepository, SchoolRepository $schoolRepository)
    {
        $this->periodRepository = $periodRepository;
        $this->schoolRepository = $schoolRepository;
    }

    public function load(ObjectManager $manager)
    {
        $schools = $this->schoolRepository->findAll();
        foreach ($schools as $school) {
            $seasons = $school->getSeasons();
            foreach ($seasons as $season) {
                $periods = [
                    'Septiembre' => [new \DateTime($season->getInitDate()->format('Y').'-09-01'), new \DateTime($season->getInitDate()->format('Y').'-09-30')],
                    'Octubre' => [new \DateTime($season->getInitDate()->format('Y').'-10-01'), new \DateTime($season->getInitDate()->format('Y').'-10-31')],
                    'Noviembre' => [new \DateTime($season->getInitDate()->format('Y').'-11-01'), new \DateTime($season->getInitDate()->format('Y').'-11-30')],
                    'Diciembre/Enero' => [new \DateTime($season->getInitDate()->format('Y').'-12-01'), new \DateTime($season->getFinishDate()->format('Y').'-01-31')],
                    'Febrero' => [new \DateTime($season->getFinishDate()->format('Y').'-02-01'), new \DateTime($season->getFinishDate()->format('Y').'-02-29')],
                    'Marzo' => [new \DateTime($season->getFinishDate()->format('Y').'-03-01'), new \DateTime($season->getFinishDate()->format('Y').'-03-31')],
                    'Abril' => [new \DateTime($season->getFinishDate()->format('Y').'-04-01'), new \DateTime($season->getFinishDate()->format('Y').'-04-30')],
                    'Mayo' => [new \DateTime($season->getFinishDate()->format('Y').'-05-01'), new \DateTime($season->getFinishDate()->format('Y').'-05-31')],
                    'Junio' => [new \DateTime($season->getFinishDate()->format('Y').'-06-01'), new \DateTime($season->getFinishDate()->format('Y').'-06-30')],
                ];
                foreach ($periods as $name => $dates) {
                    $period = (new Period())
                        ->setName($name)
                        ->setSeason($season)
                        ->setInitDate($dates[0])
                        ->setFinishDate($dates[1]);
                    for ($i = 0; $i < 4; ++$i) {
                        $period->addHoliday((new \DateTime())->setTimestamp(rand($dates[0]->getTimestamp(), $dates[1]->getTimestamp())));
                    }
                    $this->periodRepository->save($period, false);
                }
            }
        }
        $this->periodRepository->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['until_sessions', 'exclude_sessions'];
    }
}
