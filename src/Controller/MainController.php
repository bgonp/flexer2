<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\PositionRepository;
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
    public function test( // TODO: BORRAR
        StaffRepository $staffRepository,
        CustomerRepository $customerRepository,
        FamilyService $familyService,
        PositionRepository $positionRepository
    ): Response {
        dd($positionRepository->test());
        dump($staff = $staffRepository->findAll());
        dd($attendances = $staff[5]->getAssignments());
    }
}
