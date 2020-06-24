<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Season;
use App\Exception\Season\SeasonAlreadyHasPeriodsException;
use App\Service\SeasonService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api/season") */
class SeasonApiController extends BaseController
{
    /** @Route("/generate_periods", name="api_generate_periods", methods={"POST"}) */
    public function generatePeriods(Season $season, SeasonService $seasonService): JsonResponse
    {
        if (!$this->canEdit($season)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $periods = $seasonService->generatePeriods($season);
        } catch (SeasonAlreadyHasPeriodsException $e) {
            return new JsonResponse([
                'message' => 'No se pueden generar periodos de una temporada que ya tiene.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(array_values($periods->toArray()), JsonResponse::HTTP_OK);
    }
}
