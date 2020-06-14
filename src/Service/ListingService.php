<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Course;
use App\Entity\Listing;
use App\Exception\Listing\CourseAlreadyHasListing;
use App\Repository\CourseRepository;
use App\Repository\ListingRepository;
use App\Repository\SessionRepository;

class ListingService
{
    private ListingRepository $listingRepository;

    private CourseRepository $courseRepository;

    private SessionRepository $sessionRepository;

    public function __construct(
        ListingRepository $listingRepository,
        CourseRepository $courseRepository,
        SessionRepository $sessionRepository
    ) {
        $this->listingRepository = $listingRepository;
        $this->courseRepository = $courseRepository;
        $this->sessionRepository = $sessionRepository;
    }

    /**
     * @param Course[] $courses
     * @param bool $spread
     */
    public function createListingOfCourses(array $courses, bool $spread = false)
    {
        $listing = new Listing();
        $this->listingRepository->save($listing, false);
        foreach ($courses as $course) {
            if ($course->getListing()) {
                throw CourseAlreadyHasListing::create($course);
            }
            $this->courseRepository->save($course->setListing($listing), false);
            if ($spread) {
                foreach ($course->getSessions() as $session) {
                    if (!$session->getListing()) {
                        $this->sessionRepository->save($session->setListing($listing), false);
                    }
                }
            }
        }
        $this->listingRepository->flush();
    }
}
