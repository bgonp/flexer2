<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Customer;
use App\Exception\Family\CustomerAlreadyHasFamilyException;
use App\Exception\Family\CustomersAlreadyHasTheirOwnFamilyException;
use App\Repository\CustomerRepository;
use App\Service\FamilyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api/customer") */
class CustomerApiController extends BaseController
{
    /** @Route("/search", name="api_customer_search", methods={"POST"}) */
    public function search(Request $request, CustomerRepository $customerRepository): JsonResponse
    {
        if (!$this->canList(Customer::class, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }
        $customers = ($search = $request->request->get('s')) ? $customerRepository->findBySearchTerm($search) : [];

        return new JsonResponse($customers, JsonResponse::HTTP_OK);
    }

    /** @Route("/{customer_id}/add_familiar", name="api_add_familiar", methods={"POST"}) */
    public function add(Customer $customer, Customer $familiar, FamilyService $familyService): JsonResponse
    {
        if (!$this->canEdit($customer, false) || !$this->canEdit($familiar, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $family = $familyService->setCustomersAsFamily($customer, $familiar);
        } catch (CustomerAlreadyHasFamilyException | CustomersAlreadyHasTheirOwnFamilyException $e) {
            return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($family->getCustomers()->toArray(), JsonResponse::HTTP_OK);
    }

    /** @Route("/{customer_id}/remove_familiar", name="api_remove_familiar", methods={"POST"}) */
    public function remove(Customer $customer, Customer $familiar, FamilyService $familyService): JsonResponse
    {
        if (!$this->canEdit($customer, false) || !$this->canEdit($familiar, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        $family = $familyService->subtractCustomerFromFamily($familiar);

        return new JsonResponse(
            $family && !$customer->equals($familiar) ? array_values($family->getCustomers()->toArray()) : [],
            JsonResponse::HTTP_OK
        );
    }
}
