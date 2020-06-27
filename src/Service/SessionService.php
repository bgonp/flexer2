<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Course;
use App\Entity\Period;
use App\Entity\Season;
use App\Entity\Session;
use App\Exception\Course\CourseIsNotActiveException;
use App\Exception\Period\DateOutOfPeriodBoundsException;
use App\Exception\School\CourseSchoolDoesNotMatchSeasonSchoolException;
use App\Exception\Session\DuplicateSessionException;
use App\Repository\CourseRepository;
use App\Repository\SessionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class SessionService
{
    private SessionRepository $sessionRepository;

    private AttendanceService $attendanceService;

    private CourseRepository $courseRepository;

    private ?\DateInterval $interval = null;

    public function __construct(
        SessionRepository $sessionRepository,
        AttendanceService $attendanceService,
        CourseRepository $courseRepository
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->attendanceService = $attendanceService;
        $this->courseRepository = $courseRepository;
    }

    public function createSessions(Season $season, Course $course, bool $flush = true): void
    {
        if ($season->getSchool()->getId() !== $course->getSchool()->getId()) {
            throw CourseSchoolDoesNotMatchSeasonSchoolException::create($course, $season);
        }

        if (!$course->isActive()) {
            throw CourseIsNotActiveException::create($course);
        }

        // TODO: Optimizar toda la lógica...
        if (!$this->interval) {
            $this->interval = $interval = new \DateInterval('P1D');
        }

        foreach ($season->getPeriods() as $period) {
            $dates = new \DatePeriod($period->getInitDate(), $this->interval, $period->getFinishDate());
            foreach ($dates as $date) {
                $this->createSession($course, $period, $date, false);
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

    /** @throws DateOutOfPeriodBoundsException */
    public function createFromPeriodDate(Period $period, \DateTime $date): void
    {
        if ($period->getInitDate() > $date || $period->getFinishDate() < $date) {
            throw DateOutOfPeriodBoundsException::create();
        }
        $school = $period->getSeason()->getSchool();
        foreach ($this->courseRepository->findBySchoolAndDate($school, $date) as $course) {
            if ($session = $this->createSession($course, $period, $date, false)) {
                $this->attendanceService->createFromSession($session, false);
            }
        }
        $this->sessionRepository->flush();
    }

    public function removeFromPeriodDate(Period $period, \DateTime $date): void
    {
        $sessions = $this->sessionRepository->findByPeriodAndDate($period, $date);
        foreach ($sessions as $session) {
            $this->attendanceService->removeFromSession($session, false);
            $this->sessionRepository->remove($session, false);
        }
        $this->sessionRepository->flush();
    }

    private function createSession(Course $course, Period $period, \DateTime $date, bool $flush = true): ?Session
    {
        if ($this->isSessionDay($course, $period, $date)) {
            $this->sessionRepository->save(
                ($session = new Session())
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
                $flush
            );

            return $session;
        }

        return null;
    }

    private function isSessionDay(Course $course, Period $period, \DateTime $date): bool
    {
        if (
            ($course->getInitDate() && $course->getInitDate() > $date) ||
            ($course->getFinishDate() && $course->getFinishDate() < $date) ||
            $period->isHoliday($date)
        ) {
            return false;
        }
        $dom = (int) $date->format('j');
        $dow = (int) $date->format('N');
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
