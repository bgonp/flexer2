<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Course;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api/listing") */
class ListingApiController extends BaseController
{
    /** @Route("/{listing_id}/add_course", name="api_listing_add_course", methods={"POST"}) */
    public function addCourse(Listing $listing, Course $course, ListingRepository $listingRepository): JsonResponse
    {
        if (!$this->canEdit($listing, false) || !$this->canEdit($course, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        if (!$course->getListing()->equals($listing)) {
            if (1 < $course->getListing()->getCourses()->count()) {
                return new JsonResponse(
                    ['message' => 'El curso ya pertenece a otro listado'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            /** @var Course $firstCourse */
            $firstCourse = $listing->getCourses()->get(0);
            if (
                $firstCourse->getDayOfWeek() !== $course->getDayOfWeek() ||
                $firstCourse->getTime()->format('H:i') !== $course->getTime()->format('H:i') ||
                !$firstCourse->getSchool()->equals($course->getSchool()) ||
                !$firstCourse->getPlace()->equals($course->getPlace())
            ) {
                return new JsonResponse(
                    ['message' => 'Todos los cursos deben ser compatibles'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $listingRepository->save($listing->addCourse($course));
        }

        return new JsonResponse(array_values($listing->getCourses()->toArray()), JsonResponse::HTTP_OK);
    }

    /** @Route("/{listing_id}/remove_course", name="api_listing_remove_course", methods={"POST"}) */
    public function removeCourse(Listing $listing, Course $course, ListingRepository $listingRepository): JsonResponse
    {
        if (!$this->canEdit($listing, false) || !$this->canEdit($course, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        if ($course->getListing()->equals($listing)) {
            if (1 === $listing->getCourses()->count()) {
                return new JsonResponse(
                    ['message' => 'No puedes eliminar un curso si es el único del listado'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $listingRepository->save($listing->removeCourse($course));
        }

        return new JsonResponse(array_values($listing->getCourses()->toArray()), JsonResponse::HTTP_OK);
    }
}
