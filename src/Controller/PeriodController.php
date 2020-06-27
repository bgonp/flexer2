<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Period;
use App\Entity\Season;
use App\Repository\ListingRepository;
use App\Repository\PeriodRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/period") */
class PeriodController extends BaseController
{
    /** @Route("/new/{season_id}", name="period_new", methods={"GET", "POST"}) */
    public function new(Request $request, Season $season, PeriodRepository $periodRepository): Response
    {
        if (!$this->canCreate(Period::class)) {
            return $this->redirectToRoute('season_edit', ['season_id' => $season->getId()]);
        } elseif (!$this->canEdit($season)) {
            return $this->redirectToRoute('season_index');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $initDate = $request->request->get('initDate');
            $finishDate = $request->request->get('finishDate');

            if ($finishDate < $initDate) {
                $this->addFlash('error', 'La fecha final no puede ser menor que la inicial');
            } elseif (!$name) {
                $this->addFlash('error', 'El campo "nombre" es obligatorio');
            } elseif (!$initDate) {
                $this->addFlash('error', 'El campo "inicio" es obligatorio');
            } elseif (!$finishDate) {
                $this->addFlash('error', 'El campo "final" es obligatorio');
            } else {
                $period = (new Period())
                    ->setName($name)
                    ->setInitDate(new \DateTime($initDate))
                    ->setFinishDate(new \DateTime($finishDate))
                    ->setSeason($season);
                $periodRepository->save($period);

                return $this->redirectToRoute('season_edit', ['season_id' => $season->getId()]);
            }
        }

        return $this->render('period/new.html.twig', [
            'season' => $season,
            'name' => $name ?? '',
            'initDate' => $initDate ?? '',
            'finishDate' => $finishDate ?? '',
        ]);
    }

    /** @Route("/{period_id}", name="period_edit", methods={"GET", "POST"}) */
    public function edit(
        Request $request,
        Period $period,
        PeriodRepository $periodRepository,
        ListingRepository $listingRepository
    ): Response {
        if (!$this->canView($period)) {
            return $this->redirectToRoute('season_edit', ['season_id' => $period->getSeason()->getId()]);
        }

        if ($request->isMethod('POST')) {
            if (!$this->canEdit($period)) {
                return $this->redirectToRoute('season_edit', ['season_id' => $period->getSeason()->getId()]);
            }

            $name = $request->request->get('name');
            $initDate = $request->request->get('initDate');
            $finishDate = $request->request->get('finishDate');

            if ($finishDate < $initDate) {
                $this->addFlash('error', 'La fecha final no puede ser menor que la inicial');
            } elseif (!$name) {
                $this->addFlash('error', 'El campo "nombre" es obligatorio');
            } elseif (!$initDate) {
                $this->addFlash('error', 'El campo "inicio" es obligatorio');
            } elseif (!$finishDate) {
                $this->addFlash('error', 'El campo "final" es obligatorio');
            } else {
                $period
                    ->setName($name)
                    ->setInitDate(new \DateTime($initDate))
                    ->setFinishDate(new \DateTime($finishDate));
                $periodRepository->save($period);
            }
        }

        return $this->render('period/edit.html.twig', [
            'period' => $period,
            'listings' => $listingRepository->findByPeriod($period),
            'canEdit' => $this->canEdit($period),
        ]);
    }

    /** @Route("/{period_id}/delete", name="period_delete", methods={"GET"}) */
    public function delete(Period $period, PeriodRepository $periodRepository): Response
    {
        if (!$this->canDelete($period)) {
            return $this->redirectToRoute('period_edit', ['period_id' => $period->getId()]);
        }
        try {
            $periodRepository->remove($period);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar un periodo si tiene clases asociadas.');

            return $this->redirectToRoute('period_edit', ['period_id' => $period->getId()]);
        }

        return $this->redirectToRoute('season_edit', ['season_id' => $period->getSeason()->getId()]);
    }
}
