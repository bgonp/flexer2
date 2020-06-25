<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Listing;
use App\Entity\Period;
use App\Exception\Common\PageOutOfBoundsException;
use App\Repository\AgeRepository;
use App\Repository\CourseRepository;
use App\Repository\CustomerRepository;
use App\Repository\DisciplineRepository;
use App\Repository\LevelRepository;
use App\Repository\PlaceRepository;
use App\Repository\SchoolRepository;
use App\Repository\SessionRepository;
use App\Repository\StaffRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
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
