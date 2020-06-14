<?php

namespace App\DataFixtures;

use App\Repository\AttendanceRepository;
use App\Repository\CustomerRepository;
use App\Service\AttendanceService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AttendanceFixtures extends Fixture implements DependentFixtureInterface
{
    private AttendanceRepository $attendanceRepository;

    private AttendanceService $attendanceService;

    private CustomerRepository $customerRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        AttendanceService $attendanceService,
        CustomerRepository $customerRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->attendanceService = $attendanceService;
        $this->customerRepository = $customerRepository;
    }

    public function load(ObjectManager $manager)
    {
        $customers = $this->customerRepository->findAll();
        foreach ($customers as $customer) {
            $assignments = $customer->getAssignments();
            foreach ($assignments as $assignment) {
                $this->attendanceService->createAttendances($assignment, false);
            }
        }
        $this->attendanceRepository->flush();
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class,
            AssignmentFixtures::class,
        ];
    }
}
