<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Discipline;
use App\Repository\DisciplineRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/discipline") */
class DisciplineController extends BaseController
{
    /** @Route("/", name="discipline_index", methods={"GET"}) */
    public function index(DisciplineRepository $disciplineRepository): Response
    {
        if (!$this->canList(Discipline::class)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('discipline/index.html.twig', ['disciplines' => $disciplineRepository->findAll()]);
    }

    /** @Route("/{id}", name="discipline_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Discipline $discipline, DisciplineRepository $disciplineRepository): Response
    {
        if (!$this->canView($discipline)) {
            return $this->redirectToRoute('discipline_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($discipline)) {
                return $this->redirectToRoute('discipline_edit', ['id' => $discipline->getId()]);
            }
            $disciplineRepository->save($discipline
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'))
            );
        }

        return $this->render('discipline/edit.html.twig', [
            'discipline' => $discipline,
            'canEdit' => $this->canEdit($discipline),
        ]);
    }
}
