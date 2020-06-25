<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CustomerPositionRepository;
use App\Repository\CustomerRepository;
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
        CustomerPositionRepository $customerPositionRepository
    ): Response {
        dump($customerPositionRepository->findAll());
        return $this->render('main/index.html.twig');
    }
}
