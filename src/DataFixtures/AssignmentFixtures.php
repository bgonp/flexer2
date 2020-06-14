<?php

namespace App\DataFixtures;

use App\Entity\Assignment;
use App\Entity\Position;
use App\Repository\AssignmentRepository;
use App\Repository\CourseRepository;
use App\Repository\CustomerRepository;
use App\Repository\PositionRepository;
use App\Repository\StaffRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssignmentFixtures extends Fixture implements DependentFixtureInterface
{
    private AssignmentRepository $assignmentRepository;

    private CustomerRepository $customerRepository;

    private StaffRepository $staffRepository;

    private CourseRepository $courseRepository;

    private PositionRepository $positionRepository;

    public function __construct(
        AssignmentRepository $assignmentRepository,
        CustomerRepository $customerRepository,
        StaffRepository $staffRepository,
        CourseRepository $courseRepository,
        PositionRepository $positionRepository
    ) {
        $this->assignmentRepository = $assignmentRepository;
        $this->customerRepository = $customerRepository;
        $this->staffRepository = $staffRepository;
        $this->courseRepository = $courseRepository;
        $this->positionRepository = $positionRepository;
    }

    public function load(ObjectManager $manager)
    {
        $customers = $this->customerRepository->findAll();
        $staffs = $this->staffRepository->findAll();
        $courses = $this->courseRepository->findAll();
        $customerPosition = $this->positionRepository->findAllForCustomers()[0];
        $staffPosition = $this->positionRepository->findAllForStaffs()[0];
        foreach ($customers as $customer) {
            if (!rand(0, 20)) {
                continue;
            }
            $this->assignmentRepository->save(
                (new Assignment())
                    ->setCustomer($customer)
                    ->setCourse($courses[rand(0, count($courses) - 1)])
                    ->setPosition($customerPosition),
                false
            );
        }
        $this->assignmentRepository->flush();
        $this->setStaff($courses, $staffs, $staffPosition);
    }

    private function setStaff(array $courses, array $staffs, Position $position)
    {
        $this->assignmentRepository->save((new Assignment())
            ->setCustomer($staffs[0])
            ->setCourse($courses[0])
            ->setPosition($position));
        /*foreach ($courses as $course) {
            $assignment = new Assignment($staffs[rand(0, count($staffs) - 1)], $course, $position);
            if (!rand(0, 5) && $sessions = $course->getSessions()) {
                $operation = rand(0, 2);
                if ($operation > 0) {
                    $assignment->setFirstSession($sessions[rand(0, (count($sessions) - 1) / 2)]);
                }
                if ($operation < 2) {
                    $assignment->setLastSession($sessions[rand((count($sessions) - 1) / 2, count($sessions) - 1)]);
                }
            }
            $this->assignmentRepository->save($assignment, false);
        }
        $this->assignmentRepository->flush();*/
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
