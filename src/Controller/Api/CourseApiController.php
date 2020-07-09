<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Course;
use App\Entity\Listing;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api/course") */
class CourseApiController extends BaseController
{
    /** @Route("/search", name="api_course_search", methods={"POST"}) */
    public function search(Request $request, CourseRepository $courseRepository): JsonResponse
    {
        if (!$this->canList(Course::class, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }
        $courses = ($search = $request->request->get('s')) ? $courseRepository->findBySearchTerm($search) : [];

        return new JsonResponse($courses, JsonResponse::HTTP_OK);
    }

    /** @Route("/{listing_id}/search", name="api_course_compatible_search", methods={"POST"}) */
    public function searchCompatible(Listing $listing, Request $request, CourseRepository $courseRepository): JsonResponse
    {
        if (!$this->canList(Course::class, false)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        /** @var Course $course */
        $course = $listing->getCourses()->get(0);
        $courses = ($search = $request->request->get('s'))
            ? $courseRepository->findCompatibleBySearchTerm($course, $search)
            : [];

        return new JsonResponse($courses, JsonResponse::HTTP_OK);
    }
}
