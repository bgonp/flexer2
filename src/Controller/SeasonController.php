<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Season;
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
    public function new(Request $request, SeasonRepository $seasonRepository): Response
    {
        if (!$this->canCreate(Season::class)) {
            return $this->redirectToRoute('season_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $seasonRepository->save($season = (new Season())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                );

                return $this->redirectToRoute('season_edit', ['id' => $season->getId()]);
            }
        }

        return $this->render('season/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
        ]);
    }

    /** @Route("/{id}", name="season_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Season $season, SeasonRepository $seasonRepository): Response
    {
        if (!$this->canView($season)) {
            return $this->redirectToRoute('season_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($season)) {
                return $this->redirectToRoute('season_edit', ['id' => $season->getId()]);
            }
            $season
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'));
            if (empty($season->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $seasonRepository->save($season);
            }
        }

        return $this->render('season/edit.html.twig', [
            'season' => $season,
            'canEdit' => $this->canEdit($season),
        ]);
    }

    /** @Route("/{id}/delete", name="season_delete", methods={"GET"}) */
    public function delete(Season $season, SeasonRepository $seasonRepository): Response
    {
        if (!$this->canDelete($season)) {
            return $this->redirectToRoute('season_edit', ['id' => $season->getId()]);
        }
        try {
            $seasonRepository->remove($season);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una escuela si tiene cursos asociados.');

            return $this->redirectToRoute('season_edit', ['id' => $season->getId()]);
        }

        return $this->redirectToRoute('season_index');
    }
}
