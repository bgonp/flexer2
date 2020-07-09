<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Season;
use App\Repository\CustomerRepository;
use App\Repository\SeasonRepository;
use App\Repository\SessionRepository;
use App\Repository\StaffRepository;
use App\Service\SeasonService;
use App\Service\SessionService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/listing") */
class ListingController extends BaseController
{
    /** @Route("/{listing_id}", name="listing_edit", methods={"GET"}) */
    public function edit(Listing $listing, SeasonRepository $seasonRepository): Response
    {
        if (!$this->canView($listing)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('listing/edit.html.twig', [
            'listing' => $listing,
            'seasons' => $seasonRepository->findByListingWithPeriods($listing),
            'generableSeasons' => $seasonRepository->findBySchool($listing->getCourses()->get(0)->getSchool()),
        ]);
    }

    /** @Route("/{listing_id}/generate", name="listing_generate", methods={"POST"}) */
    public function generate(
        Listing $listing,
        Season $season,
        SessionService $sessionService
    ): Response {
        if ($this->canEdit($listing)) {
            foreach ($listing->getCourses() as $course) {
                $sessionService->createSessions($season, $course);
            }
        }

        return $this->redirectToRoute('listing_edit', ['listing_id' => $listing->getId()]);
    }

    /** @Route("/{listing_id}/{period_id}", name="listing_show", methods={"GET"}) */
    public function show(
        Listing $listing,
        Period $period,
        SessionRepository $sessionRepository,
        CustomerRepository $customerRepository,
        StaffRepository $staffRepository
    ): Response {
        if (!$this->canView($listing)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('listing/period.html.twig', [
            'listing' => $listing,
            'period' => $period,
            'sessions' => $sessionRepository->findByListingAndPeriod($listing, $period),
            'customers' => $customerRepository->findByListingAndPeriod($listing, $period),
            'staffs' => $staffRepository->findByListingAndPeriod($listing, $period),
        ]);
    }
}
