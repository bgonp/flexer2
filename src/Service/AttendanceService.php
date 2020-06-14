<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Assignment;
use App\Entity\Attendance;
use App\Exception\Attendance\DuplicateAttendanceException;
use App\Repository\AttendanceRepository;
use App\Repository\SessionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class AttendanceService
{
    private AttendanceRepository $attendanceRepository;

    private SessionRepository $sessionRepository;

    public function __construct(AttendanceRepository $attendanceRepository, SessionRepository $sessionRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function createAttendances(Assignment $assignment, bool $flush = true): void
    {
        $sessions = $this->sessionRepository->findByCourseBetweenDates(
            $assignment->getCourse(),
            $assignment->getFirstSession(),
            $assignment->getLastSession()
        );
        foreach ($sessions as $session) {
            $this->attendanceRepository->save((new Attendance())
                    ->setCustomer($assignment->getCustomer())
                    ->setSession($session)
                    ->setPosition($assignment->getPosition()),
                false
            );
        }

        if ($flush) {
            try {
                $this->attendanceRepository->flush();
            } catch (UniqueConstraintViolationException $e) {
                throw DuplicateAttendanceException::create();
            }
        }
    }
}
