<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attendance;
use App\Entity\Customer;
use App\Entity\Payment;
use App\Entity\Period;
use App\Entity\Rate;
use App\Exception\Payment\PaymentCoursesDoesntMatchRealCourses;
use App\Exception\Payment\PaymentCustomersDoesntMatchFamiliarsException;
use App\Exception\Payment\PaymentPeriodsCountIsGreaterThanExistingPeriods;
use App\Exception\Payment\PaymentSessionsDoesntMatchPeriodSessions;
use App\Repository\AttendanceRepository;
use App\Repository\PeriodRepository;
use App\Repository\SessionRepository;

class PaymentService
{
    private AttendanceRepository $attendanceRepository;

    private PeriodRepository $periodRepository;

    private SessionRepository $sessionRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        PeriodRepository $periodRepository,
        SessionRepository $sessionRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->periodRepository = $periodRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function pay(Payment $payment, Attendance $attendance): void
    {
        foreach ($payment->getAttendances() as $alreadyPayedAttendance) {
            $alreadyPayedAttendance->setPayment(null);
            $this->attendanceRepository->save($alreadyPayedAttendance, false);
        }

        if ($payment->getRate()->isSpreadable()) {
            $this->spreadPayment($payment, $attendance);
        } else {
            $attendance->setPayment($payment)->setIsPaidHere(true);
            $this->attendanceRepository->save($attendance, false);
        }

        $this->attendanceRepository->flush();
    }

    private function spreadPayment(Payment $payment, Attendance $attendance): void
    {
        $rate = $payment->getRate();
        $course = 1 === $rate->getCoursesCount() ? $attendance->getSession()->getCourse() : null;
        $period = $attendance->getSession()->getPeriod();
        $periods = $this->periodRepository->findConsecutivePeriods($rate->getPeriodsCount(), $period);
        $customers = $attendance->getCustomer()->getFamily()->getCustomers();

        $attendances = $this->attendanceRepository->findUnpaidByCustomersAndPeriods($customers, $periods, $course);

        $this->checkPaymentCount($rate, $attendances);

        foreach ($attendances as $affectedAttendance) {
            if ($attendance->equals($affectedAttendance)) {
                $affectedAttendance->setIsPaidHere(true);
            }
            $affectedAttendance->setPayment($payment);
            $this->attendanceRepository->save($affectedAttendance, false);
        }
    }

    /**
     * @param Attendance[] $attendances
     */
    private function checkPaymentCount(Rate $rate, array $attendances): void
    {
        $coursesCount = $rate->getCoursesCount();
        $customersCount = $rate->getCustomersCount();
        $periodsCount = $rate->getPeriodsCount();

        $coursesIds = [];
        $periodsIds = [];
        $customers = [];

        foreach ($attendances as $attendance) {
            $customerId = $attendance->getCustomer()->getId();
            $courseId = $attendance->getSession()->getCourse()->getId();
            $periodId = $attendance->getSession()->getPeriod()->getId();

            $coursesIds[] = $courseId;
            $periodsIds[] = $periodId;

            $customers[$customerId][$periodId][$courseId][] = $attendance->getSession()->getId();
        }

        $coursesIds = array_unique($coursesIds);
        $periodsIds = array_unique($periodsIds);
        $sessionsCount = $this->sessionRepository->getCounterByCoursesAndPeriodsIds($coursesIds, $periodsIds);

        if ($customersCount > 0 && count($customers) !== $customersCount) {
            throw PaymentCustomersDoesntMatchFamiliarsException::create();
        }
        foreach ($customers as $customerId => $periods) {
            if ($periodsCount > 0 && count($periods) < $periodsCount) {
                throw PaymentPeriodsCountIsGreaterThanExistingPeriods::create();
            }
            foreach ($periods as $periodId => $courses) {
                if ($coursesCount > 0 && count($courses) !== $coursesCount) {
                    throw PaymentCoursesDoesntMatchRealCourses::create();
                }
                foreach ($courses as $courseId => $sessions) {
                    if (count($sessions) !== $sessionsCount[$courseId][$periodId]) {
                        throw PaymentSessionsDoesntMatchPeriodSessions::create();
                    }
                }
            }
        }
    }

    // TODO: Borrar to esto si lo anterior funciona...

    /*public function spreadPayment(Payment $payment, Customer $customer, Period $period, Course $course = null): void
    {
        $customers = $this->getCustomersOfPayment($payment, $customer);
        $periods = $this->getPeriodsOfPayment($payment, $period);
        $attendances = $this->attendanceRepository->getAffectedAttendances($customers, $periods, $course);
        $this->checkCoursesCount($payment, $course, $attendances);

        foreach ($attendances as $attendance) {
            $this->attendanceRepository->save($attendance->setPayment($payment), false);
        }
        $this->attendanceRepository->flush();
    }*/

    /* @return Customer[] */
    /*private function getCustomersOfPayment(Payment $payment, Customer $customer): array
    {
        $count = $payment->getRate()->getCustomersCount();

        if ($count === 1) {
            $customers = [$customer];
        } else if ($count === $customer->getFamily()->getCustomers()->count()) {
            $customers = $customer->getFamily()->getCustomers();
        } else {
            throw PaymentCustomersDoesntMatchFamiliarsException::create();
        }

        return $customers;
    }*/

    /* @return Period[] */
    /*private function getPeriodsOfPayment(Payment $payment, Period $period): array
    {
        $count = $payment->getRate()->getPeriodsCount();

        if ($count === 1) {
            $periods = [$period];
        } else {
            $periods = $this->periodRepository->getConsecutivePeriods($count, $period);
            if (count($periods) < $count) {
                throw PaymentPeriodsCountIsGreaterThanExistingPeriods::create();
            }
        }

        return $periods;
    }*/

    /* @param Attendance[] $attendances */
    /*private function checkCoursesCount(Payment $payment, ?Course $course, array $attendances): void
    {
        $count = $payment->getRate()->getCoursesCount();
        if ($count === 0 || (!is_null($course) && $count === 1)) {
            return;
        }

        $courses = [];
        foreach ($attendances as $attendance) {
            $customerId = $attendance->getCustomer()->getId();
            $courseId = $attendance->getSession()->getCourse()->getId();
            $periodId = $attendance->getSession()->getPeriod()->getId();
            if (!is_array($courses[$customerId])) {
                $courses[$customerId] = [];
            }
            if (!is_array($courses[$customerId][$periodId])) {
                $courses[$customerId][$periodId] = [];
            }
            if (!in_array($courseId, $courses[$customerId][$periodId])) {
                $courses[$customerId][$periodId][] = $courseId;
            }
        }

        foreach ($courses as $customerCourses) {
            foreach ($customerCourses as $periodCourses) {
                if (count($periodCourses) !== $count) {
                    throw PaymentCoursesDoesntMatchRealCourses::create();
                }
            }
        }
    }*/
}
