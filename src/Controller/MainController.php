<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
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

    /** @Route("/test/{familiar_id}", name="test") */
    public function test(// TODO: BORRAR
        Customer $familiar,
        StaffRepository $staffRepository,
        CustomerRepository $customerRepository,
        FamilyService $familyService,
        CustomerPositionRepository $customerPositionRepository,
        SessionRepository $sessionRepository,
        PeriodRepository $periodRepository
    ): Response {
        dump($familiar);

        return $this->render('main/index.html.twig');
    }
}
