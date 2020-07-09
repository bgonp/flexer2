<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Season;
use App\Repository\PeriodRepository;
use App\Repository\SchoolRepository;
use App\Repository\SeasonRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/season") */
class SeasonController extends BaseController
{
    /** @Route("/{page<[1-9]\d*>}", name="season_index", methods={"GET"}) */
    public function index(
        Request $request,
        SeasonRepository $seasonRepository,
        SchoolRepository $schoolRepository,
        int $page = 1
    ): Response {
        if (!$this->canList(Season::class)) {
            return $this->redirectToRoute('main');
        }

        if ($request->query->has('s')) {
            if ((!$schoolId = $request->query->get('s')) || (!$school = $schoolRepository->find($schoolId))) {
                return $this->redirectToRoute('season_index');
            }
            $seasons = $seasonRepository->findBySchoolPaged($school, $page);
        } else {
            $seasons = $seasonRepository->findAllPaged($page);
        }

        return $this->render('season/index.html.twig', [
            'school' => $school ?? null,
            'seasons' => $seasons->getResults(),
            'currentPage' => $seasons->getPage(),
            'lastPage' => $seasons->getLastPage(),
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    /** @Route("/new", name="season_new", methods={"GET", "POST"}) */
    public function new(
        Request $request,
        SeasonRepository $seasonRepository,
        SchoolRepository $schoolRepository
    ): Response {
        if (!$this->canCreate(Season::class)) {
            return $this->redirectToRoute('season_index');
        }

        if ($request->isMethod('POST')) {
            if (!$school = $schoolRepository->find($request->request->get('school'))) {
                $this->addFlash('error', 'El campo "escuela" es obligatorio');
            } elseif (!$initDate = $request->request->get('initDate')) {
                $this->addFlash('error', 'El campo "inicio" es obligatorio');
            } elseif (!$finishDate = $request->request->get('finishDate')) {
                $this->addFlash('error', 'El campo "final" es obligatorio');
            } elseif ($finishDate < $initDate) {
                $this->addFlash('error', 'La fecha final no puede ser menor que la inicial');
            } else {
                $seasonRepository->save($season = (new Season())
                    ->setSchool($school)
                    ->setInitDate(new \DateTime($initDate))
                    ->setFinishDate(new \DateTime($finishDate))
                );

                return $this->redirectToRoute('season_edit', ['season_id' => $season->getId()]);
            }
        }

        return $this->render('season/new.html.twig', [
            'school' => $school ?? null,
            'initDate' => $initDate ?? '',
            'finishDate' => $finishDate ?? '',
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    /** @Route("/{season_id}", name="season_edit", methods={"GET", "POST"}) */
    public function edit(
        Request $request,
        Season $season,
        SeasonRepository $seasonRepository,
        SchoolRepository $schoolRepository,
        PeriodRepository $periodRepository
    ): Response {
        if (!$this->canView($season)) {
            return $this->redirectToRoute('season_index');
        }

        if ($request->isMethod('POST')) {
            if (!$this->canEdit($season)) {
                return $this->redirectToRoute('season_edit', ['season_id' => $season->getId()]);
            }

            if (!$school = $schoolRepository->find($request->request->get('school'))) {
                $this->addFlash('error', 'El campo "escuela" es obligatorio');
            } elseif (!$initDate = $request->request->get('initDate')) {
                $this->addFlash('error', 'El campo "inicio" es obligatorio');
            } elseif (!$finishDate = $request->request->get('finishDate')) {
                $this->addFlash('error', 'El campo "final" es obligatorio');
            } elseif ($finishDate < $initDate) {
                $this->addFlash('error', 'La fecha final no puede ser menor que la inicial');
            } else {
                $seasonRepository->save($season
                    ->setSchool($school)
                    ->setInitDate(new \DateTime($initDate))
                    ->setFinishDate(new \DateTime($finishDate))
                );
            }
        }

        return $this->render('season/edit.html.twig', [
            'season' => $season,
            'schools' => $schoolRepository->findAll(),
            'periods' => $periodRepository->findBySeason($season),
            'canEdit' => $this->canEdit($season),
        ]);
    }

    /** @Route("/{season_id}/delete", name="season_delete", methods={"GET"}) */
    public function delete(Season $season, SeasonRepository $seasonRepository): Response
    {
        if (!$this->canDelete($season)) {
            return $this->redirectToRoute('season_edit', ['season_id' => $season->getId()]);
        }
        try {
            $seasonRepository->remove($season);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una escuela si tiene cursos asociados.');

            return $this->redirectToRoute('season_edit', ['season_id' => $season->getId()]);
        }

        return $this->redirectToRoute('season_index');
    }
}
