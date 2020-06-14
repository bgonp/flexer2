<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Repository\AttendanceRepository;
use App\Repository\CourseRepository;
use App\Repository\PaymentRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    private PaymentRepository $paymentRepository;

    private CourseRepository $courseRepository;

    private AttendanceRepository $attendanceRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        CourseRepository $courseRepository,
        AttendanceRepository $attendanceRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->courseRepository = $courseRepository;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function load(ObjectManager $manager)
    {
        $courses = $this->courseRepository->findAll();
        foreach ($courses as $course) {
            $rates = $course->getSchool()->getRates();
            $sessions = $course->getSessions();
            foreach ($sessions as $session) {
                $attendances = $session->getAttendances();
                foreach ($attendances as $attendance) {
                    if ($rand = rand(0, 5)) {
                        $rate = $rates[rand(0, count($rates) - 1)];
                        $payment = (new Payment())
                            ->setRate($rate)
                            ->setAmount($rate->getAmount())
                            ->setIsTransfer($rand > 1);
                        $this->paymentRepository->save($payment, false);
                        $this->attendanceRepository->save($attendance->setPayment($payment), false);
                    }
                }
            }
        }
        $this->paymentRepository->flush();
        $this->attendanceRepository->flush();
    }

    public function getDependencies()
    {
        return [
            CourseFixtures::class,
            SessionFixtures::class,
            AttendanceFixtures::class,
            RateFixtures::class,
        ];
    }
}
