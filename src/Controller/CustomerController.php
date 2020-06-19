<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer")
 */
class CustomerController extends BaseController
{
    /** @Route("/{page<\d+>}", name="customer_index", methods={"GET"}) */
    public function index(Request $request, CustomerRepository $customerRepository, int $page = 1): Response
    {
        if (!$this->canList(Customer::class)) {
            return $this->redirectToRoute('main');
        }
        if ($request->query->has('s')) {
            if (!$search = $request->query->get('s')) {
                return $this->redirectToRoute('customer_index');
            }
            if ($page > $lastPage = $customerRepository->getLastPageBySearchTerm($search)) {
                return $this->redirectToRoute('customer_index', ['s' => $search, 'page' => $lastPage]);
            }
            $customers = $customerRepository->findBySearchTerm($search, $page);
        } else {
            if ($page > $lastPage = $customerRepository->getLastPage()) {
                return $this->redirectToRoute('customer_index', ['page' => $lastPage]);
            }
            $customers = $customerRepository->findAll($page);
        }

        return $this->render('customer/index.html.twig', [
            'search' => $search ?? '',
            'customers' => $customers,
            'currentPage' => $page,
            'lastPage' => $lastPage,
        ]);
    }

    /** @Route("/{id}", name="customer_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if (!$this->canView($customer)) {
            return $this->redirectToRoute('customer_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($customer)) {
                return $this->redirectToRoute('customer_edit', ['id' => $customer->getId()]);
            }
            $customerRepository->save($customer
                ->setName($request->request->get('name'))
                ->setSurname($request->request->get('surname'))
                ->setBirthdate(new \DateTime($request->request->get('birthdate')))
                ->setEmail($request->request->get('email'))
                ->setPhone($request->request->get('phone'))
                ->setNotes($request->request->get('notes'))
            );
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'familiars' => $customer->getFamily() ? $customerRepository->findByFamiliar($customer) : [],
            'canEdit' => $this->canEdit($customer),
        ]);
    }
}
