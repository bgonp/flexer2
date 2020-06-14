<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Course;
use App\Entity\Period;
use App\Entity\Season;
use App\Entity\Session;
use App\Exception\Course\CourseIsNotActiveException;
use App\Exception\School\CourseSchoolDoesNotMatchSeasonSchoolException;
use App\Exception\Session\DuplicateSessionException;
use App\Repository\SessionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class SessionService
{
    private SessionRepository $sessionRepository;

    private ?\DateInterval $interval = null;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function createSessions(Season $season, Course $course, bool $flush = true): void
    {
        if ($season->getSchool()->getId() !== $course->getSchool()->getId()) {
            throw CourseSchoolDoesNotMatchSeasonSchoolException::create($course, $season);
        }

        if (!$course->isActive()) {
            throw CourseIsNotActiveException::create($course);
        }

        if (!$this->interval) {
            $this->interval = $interval = new \DateInterval('P1D');
        }

        $periods = $season->getPeriods();
        foreach ($periods as $period) {
            $dates = new \DatePeriod($period->getInitDate(), $this->interval, $period->getFinishDate());
            foreach ($dates as $date) {
                if ($this->isSessionDay($course, $period, $date)) {
                    $this->sessionRepository->save(
                        (new Session())
                            ->setCourse($course)
                            ->setPeriod($period)
                            ->setDay($date)
                            ->setTime($course->getTime())
                            ->setDuration($course->getDuration())
                            ->setPlace($course->getPlace())
                            ->setDiscipline($course->getDiscipline())
                            ->setLevel($course->getLevel())
                            ->setAge($course->getAge())
                            ->setListing($course->getListing()),
                        false
                    );
                }
            }
        }

        if ($flush) {
            try {
                $this->sessionRepository->flush();
            } catch (UniqueConstraintViolationException $e) {
                throw DuplicateSessionException::create();
            }
        }
    }

    private function isSessionDay(Course $course, Period $period, \DateTime $date): bool
    {
        if ($period->isHoliday($date)) {
            return false;
        }
        $dom = date_format($date, 'j');
        $dow = date_format($date, 'N');
        if (!empty($course->getDayOfWeek()) && !in_array($dow, $course->getDayOfWeek())) {
            return false;
        }
        if (empty($course->getWeekOfMonth())) {
            return true;
        }
        $womPos = ceil(($dom - $dow) / 7) + 1;
        $womNeg = ceil(($date->format('t') - $dom - $dow) / 7);

        return in_array($womPos, $course->getWeekOfMonth()) || in_array($womNeg, $course->getWeekOfMonth());
    }
}
