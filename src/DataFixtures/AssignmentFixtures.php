<?php

namespace App\DataFixtures;

use App\Entity\Assignment;
use App\Entity\Position;
use App\Entity\StaffPosition;
use App\Repository\AssignmentRepository;
use App\Repository\CourseRepository;
use App\Repository\CustomerPositionRepository;
use App\Repository\CustomerRepository;
use App\Repository\StaffPositionRepository;
use App\Repository\StaffRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssignmentFixtures extends Fixture implements DependentFixtureInterface
{
    private CustomerRepository $customerRepository;

    private StaffRepository $staffRepository;

    private CourseRepository $courseRepository;

    private CustomerPositionRepository $customerPositionRepository;

    private StaffPositionRepository $staffPositionRepository;

    private AssignmentRepository $assignmentRepository;

    public function __construct(
        AssignmentRepository $assignmentRepository,
        CustomerRepository $customerRepository,
        StaffRepository $staffRepository,
        CourseRepository $courseRepository,
        CustomerPositionRepository $customerPositionRepository,
        StaffPositionRepository $staffPositionRepository
    ) {
        $this->assignmentRepository = $assignmentRepository;
        $this->customerRepository = $customerRepository;
        $this->staffRepository = $staffRepository;
        $this->courseRepository = $courseRepository;
        $this->customerPositionRepository = $customerPositionRepository;
        $this->staffPositionRepository = $staffPositionRepository;
    }

    public function load(ObjectManager $manager)
    {
        $customers = $this->customerRepository->findAllNoStaff();
        $staffs = $this->staffRepository->findAll();
        $courses = $this->courseRepository->findAll();
        $customerPosition = $this->customerPositionRepository->findAll()[0];
        $staffPosition = $this->staffPositionRepository->findAll()[0];

        $this->setAssignments($courses, $customers, $customerPosition);
        $this->setAssignments($courses, $staffs, $staffPosition);
        $this->assignmentRepository->flush();
    }

    private function setAssignments(array $courses, array $customers, Position $position): void
    {
        foreach ($courses as $course) {
            shuffle($customers);
            $count = StaffPosition::class === get_class($position) ? 1 : rand(4, 12);
            for ($i = 0; $i < $count; ++$i) {
                $this->assignmentRepository->save(
                    (new Assignment())
                        ->setCourse($course)
                        ->setCustomer($customers[$i])
                        ->setPosition($position),
                    false
                );
            }
        }
    }

    public function getDependencies()
    {
        return [
            CourseFixtures::class,
            CustomerFixtures::class,
            StaffFixtures::class,
            PositionFixtures::class,
        ];
    }
}
