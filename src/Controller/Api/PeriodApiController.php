<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Period;
use App\Repository\PeriodRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api/period") */
class PeriodApiController extends BaseController
{
    /** @Route("/remove", name="api_remove_period", methods={"POST"}) */
    public function remove(Period $period, PeriodRepository $periodRepository): JsonResponse
    {
        if (!$this->canDelete($period)) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $periodRepository->remove($period);
            $periods = $period->getSeason()->getPeriods()->toArray();
            usort($periods, function ($a, $b) { return $a->getInitDate() <=> $b->getInitDate(); });
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse([
                'message' => 'No se puede eliminar un periodo si tiene clases asociadas.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(array_values($periods), JsonResponse::HTTP_OK);
    }

    /** @Route("/{id}/remove_holiday", name="api_remove_holiday", methods={"POST"}) */
    public function removeHoliday(Period $period, Request $request, PeriodRepository $periodRepository): JsonResponse
    {
        $holiday = new \DateTime($request->request->get('holiday'));
        if ($holiday < $period->getInitDate() || $holiday > $period->getFinishDate()) {
            return new JsonResponse([
                'message' => 'La fecha tiene que estar dentro de los límites del periodo',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $periodRepository->save($period->removeHoliday($holiday));
        return new JsonResponse($period->getHolidays(), JsonResponse::HTTP_OK);
    }

    /** @Route("/{id}/add_holiday", name="api_add_holiday", methods={"POST"}) */
    public function addHoliday(Period $period, Request $request, PeriodRepository $periodRepository): JsonResponse
    {
        $holiday = new \DateTime($request->request->get('holiday'));
        if ($holiday < $period->getInitDate() || $holiday > $period->getFinishDate()) {
            return new JsonResponse([
                'message' => 'La fecha tiene que estar dentro de los límites del periodo',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $periodRepository->save($period->addHoliday($holiday));
        return new JsonResponse($period->getHolidays(), JsonResponse::HTTP_OK);
    }
}
