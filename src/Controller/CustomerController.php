<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Exception\Common\PageOutOfBoundsException;
use App\Repository\CustomerRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer")
 */
class CustomerController extends BaseController
{
    /** @Route("/{page<[1-9]\d*>}", name="customer_index", methods={"GET"}) */
    public function index(Request $request, CustomerRepository $customerRepository, int $page = 1): Response
    {
        // TODO: Add custom order
        if (!$this->canList(Customer::class)) {
            return $this->redirectToRoute('main');
        }

        if ($request->query->has('s')) {
            if (!$search = $request->query->get('s')) {
                return $this->redirectToRoute('customer_index');
            }

            try {
                $customers = $customerRepository->findBySearchTermPaged($search, $page);
            } catch (PageOutOfBoundsException $e) {
                return $this->redirectToRoute('customer_index', ['s' => $search]);
            }
        } else {
            try {
                $customers = $customerRepository->findAllPaged($page);
            } catch (PageOutOfBoundsException $e) {
                return $this->redirectToRoute('customer_index');
            }
        }

        return $this->render('customer/index.html.twig', [
            'search' => $search ?? '',
            'customers' => $customers->getResults(),
            'currentPage' => $customers->getPage(),
            'lastPage' => $customers->getLastPage(),
        ]);
    }

    /** @Route("/new", name="customer_new", methods={"GET", "POST"}) */
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        if (!$this->canCreate(Customer::class)) {
            return $this->redirectToRoute('customer_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $surname = $request->request->get('surname');
            $birthdate = $request->request->get('birthdate');
            $email = $request->request->get('email');
            $phone = $request->request->get('phone');
            $notes = $request->request->get('notes');

            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $customerRepository->save($customer = (new Customer())
                    ->setName($name)
                    ->setSurname($surname)
                    ->setBirthdate($birthdate ? new \DateTime($birthdate) : null)
                    ->setEmail($email)
                    ->setPhone($phone)
                    ->setNotes($notes)
                );

                return $this->redirectToRoute('customer_edit', ['customer_id' => $customer->getId()]);
            }
        }

        return $this->render('customer/new.html.twig', [
            'name' => $name ?? '',
            'surname' => $surname ?? '',
            'birthdate' => $birthdate ?? '',
            'email' => $email ?? '',
            'phone' => $phone ?? '',
            'notes' => $notes ?? '',
        ]);
    }

    /** @Route("/{customer_id}", name="customer_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if (!$this->canView($customer)) {
            return $this->redirectToRoute('customer_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($customer)) {
                return $this->redirectToRoute('customer_edit', ['customer_id' => $customer->getId()]);
            }
            $customer
                ->setName($request->request->get('name'))
                ->setSurname($request->request->get('surname'))
                ->setBirthdate(new \DateTime($request->request->get('birthdate')))
                ->setEmail($request->request->get('email'))
                ->setPhone($request->request->get('phone'))
                ->setNotes($request->request->get('notes'));
            if (empty($customer->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $customerRepository->save($customer);
            }
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'familiars' => $customer->getFamily() ? $customerRepository->findByFamiliar($customer) : [],
            'canEdit' => $this->canEdit($customer),
        ]);
    }

    /** @Route("/{customer_id}/delete", name="customer_delete", methods={"GET"}) */
    public function delete(Customer $customer, CustomerRepository $customerRepository): Response
    {
        if (!$this->canDelete($customer)) {
            return $this->redirectToRoute('customer_edit', ['customer_id' => $customer->getId()]);
        }
        try {
            $customerRepository->remove($customer);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar un alumno si tiene historial.');

            return $this->redirectToRoute('customer_edit', ['customer_id' => $customer->getId()]);
        }

        return $this->redirectToRoute('customer_index');
    }
}
