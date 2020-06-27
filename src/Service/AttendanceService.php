<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Assignment;
use App\Entity\Attendance;
use App\Entity\Session;
use App\Exception\Attendance\DuplicateAttendanceException;
use App\Repository\AssignmentRepository;
use App\Repository\AttendanceRepository;
use App\Repository\SessionRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class AttendanceService
{
    private AttendanceRepository $attendanceRepository;

    private SessionRepository $sessionRepository;

    private AssignmentRepository $assignmentRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        SessionRepository $sessionRepository,
        AssignmentRepository $assignmentRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->sessionRepository = $sessionRepository;
        $this->assignmentRepository = $assignmentRepository;
    }

    /** @throws DuplicateAttendanceException */
    public function createFromAssignment(Assignment $assignment, bool $flush = true): void
    {
        $sessions = $this->sessionRepository->findByCourseBetweenSessions(
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

    public function createFromSession(Session $session, bool $flush = true): void
    {
        $assignments = $this->assignmentRepository->findByCourseAndDate($session->getCourse(), $session->getDay());
        foreach ($assignments as $assignment) {
            $this->attendanceRepository->save((new Attendance())
                ->setCustomer($assignment->getCustomer())
                ->setSession($session)
                ->setPosition($assignment->getPosition()),
                false
            );
        }
        if ($flush) {
            $this->attendanceRepository->flush();
        }
    }

    public function removeFromSession(Session $session, bool $flush = true): void
    {
        foreach ($session->getAttendances() as $attendance) {
            $this->attendanceRepository->remove($attendance, false);
        }
        if ($flush) {
            $this->attendanceRepository->flush();
        }
    }
}
