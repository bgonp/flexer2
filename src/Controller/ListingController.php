<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Listing;
use App\Entity\Period;
use App\Repository\CustomerRepository;
use App\Repository\SessionRepository;
use App\Repository\StaffRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/listing") */
class ListingController extends BaseController
{
    /** @Route("/{listing_id}/{period_id}", name="listing_edit", methods={"GET"}) */
    public function index(
        Listing $listing,
        Period $period,
        SessionRepository $sessionRepository,
        CustomerRepository $customerRepository,
        StaffRepository $staffRepository
    ): Response {
        if (!$this->canEdit($listing)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('listing/edit.html.twig', [
            'listing' => $listing,
            'period' => $period,
            'sessions' => $sessionRepository->findByListingAndPeriod($listing, $period),
            'customers' => $customerRepository->findByListingAndPeriod($listing, $period),
            'staffs' => $staffRepository->findByListingAndPeriod($listing, $period),
        ]);
    }
}
