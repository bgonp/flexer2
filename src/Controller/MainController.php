<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CourseRepository;
use App\Repository\CustomerPositionRepository;
use App\Repository\CustomerRepository;
use App\Repository\PeriodRepository;
use App\Repository\SessionRepository;
use App\Repository\StaffRepository;
use App\Service\FamilyService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends BaseController
{
    /** @Route("/", name="main", methods={"GET"}) */
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    /** @Route("/test", name="test") */
    public function test(// TODO: BORRAR
        StaffRepository $staffRepository,
        CustomerRepository $customerRepository,
        FamilyService $familyService,
        CustomerPositionRepository $customerPositionRepository,
        SessionRepository $sessionRepository,
        PeriodRepository $periodRepository,
        CourseRepository $courseRepository
    ): Response {
        $c1 = $courseRepository->find('9ba651bc-2bca-4f1e-b2c0-0302f692d5bc');
        $c2 = $courseRepository->find('1de76de6-3fb9-4f90-bd30-375955ca5749');
        dump($c1->getTime());
        dump($c2->getTime());
        dump($c1->getTime() === $c1->getTime());

        return $this->render('main/index.html.twig');
    }
}
