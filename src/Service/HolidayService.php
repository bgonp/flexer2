<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Period;
use App\Exception\Period\DateOutOfPeriodBoundsException;
use App\Exception\Period\FutureDateException;
use App\Repository\AttendanceRepository;
use App\Repository\PeriodRepository;
use App\Repository\SessionRepository;

class HolidayService
{
    private PeriodRepository $periodRepository;

    private AttendanceRepository $attendanceRepository;

    private SessionRepository $sessionRepository;

    private SessionService $sessionService;

    private \DateTime $today;

    public function __construct(
        PeriodRepository $periodRepository,
        AttendanceRepository $attendanceRepository,
        SessionRepository $sessionRepository,
        SessionService $sessionService
    ) {
        $this->periodRepository = $periodRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->sessionRepository = $sessionRepository;
        $this->sessionService = $sessionService;
    }

    /** @throws DateOutOfPeriodBoundsException|FutureDateException */
    public function addToPeriod(\DateTime $date, Period $period, bool $onlyFuture = true): void
    {
        if ($date < $period->getInitDate() || $date > $period->getFinishDate()) {
            throw DateOutOfPeriodBoundsException::create();
        }
        if ($onlyFuture && !$this->isFuture($date)) {
            throw FutureDateException::create();
        }
        if (!$period->isHoliday($date)) {
            $period->addHoliday($date);
            $this->periodRepository->save($period, false);
            $this->sessionService->removeFromPeriodDate($period, $date);
        }
    }

    /** @throws DateOutOfPeriodBoundsException|FutureDateException */
    public function removeFromPeriod(\DateTime $date, Period $period, bool $onlyFuture = true): void
    {
        if ($onlyFuture && !$this->isFuture($date)) {
            throw FutureDateException::create();
        }
        $period->removeHoliday($date);
        $this->periodRepository->save($period, false);
        $this->sessionService->createFromPeriodDate($period, $date);
    }

    private function isFuture(\DateTime $date): bool
    {
        if (!isset($this->today)) {
            $this->today = (new \DateTime())->setTime(0, 0, 0);
        }

        return $date > $this->today;
    }
}
