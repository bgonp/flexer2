<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Period;
use App\Entity\Season;
use App\Exception\Season\SeasonAlreadyHasPeriodsException;
use App\Repository\PeriodRepository;
use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\Collection;

class SeasonService
{
    private PeriodRepository $periodRepository;

    private SeasonRepository $seasonRepository;

    public function __construct(PeriodRepository $periodRepository, SeasonRepository $seasonRepository)
    {
        $this->periodRepository = $periodRepository;
        $this->seasonRepository = $seasonRepository;
    }

    public function generatePeriods(Season $season): Collection
    {
        if ($season->getPeriods()->count() > 0) {
            throw SeasonAlreadyHasPeriodsException::create();
        }

        $currentDate = clone $season->getInitDate();
        $interval = new \DateInterval('P1D');
        $period = null;
        while ($season->getFinishDate() > $currentDate) {
            $name = ucfirst(\IntlDateFormatter::formatObject($currentDate, 'LLLL'));
            $period = (new Period())
                ->setName($name)
                ->setInitDate(clone $currentDate);
            $day = (int) $currentDate->format('t');
            $month = (int) $currentDate->format('n');
            $year = (int) $currentDate->format('Y');
            $currentDate->setDate($year, $month, $day);
            $period->setFinishDate(clone $currentDate);
            $season->addPeriod($period);
            $this->periodRepository->save($period, false);
            $currentDate->add($interval);
        }
        $this->seasonRepository->save($season);

        return $season->getPeriods();
    }
}
