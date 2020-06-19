<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CustomerRepository;
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
    public function test(CustomerRepository $repository, FamilyService $familyService): Response
    {
        $customers = $repository->findAll(2);
        for ($i = 0; $i < 20; ++$i) {
            $customer = $customers[$i];
            if (($family = $customer->getFamily()) && $family->getCustomers()->count() > 2) {
                break;
            }
        }
        $customer = $family->getCustomers()->get(1);

        dump($family->getId());
        dump($family->getCustomers()->toArray());
        dump(json_encode(array_values($family->getCustomers()->toArray())));
        $family = $familyService->subtractCustomerFromFamily($customer);
        dump($family->getId());
        dump($family->getCustomers()->toArray());
        dd(json_encode(array_values($family->getCustomers()->toArray())));
    }
}
