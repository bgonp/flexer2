<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Discipline;
use App\Repository\DisciplineRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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

    /** @Route("/new", name="discipline_new", methods={"GET", "POST"}) */
    public function new(Request $request, DisciplineRepository $disciplineRepository): Response
    {
        if (!$this->canCreate(Discipline::class)) {
            return $this->redirectToRoute('discipline_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $disciplineRepository->save($discipline = (new Discipline())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                );

                return $this->redirectToRoute('discipline_edit', ['id' => $discipline->getId()]);
            }
        }

        return $this->render('discipline/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
        ]);
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

    /** @Route("/{id}/delete", name="discipline_delete", methods={"GET"}) */
    public function delete(Discipline $discipline, DisciplineRepository $disciplineRepository): Response
    {
        if (!$this->canDelete($discipline)) {
            return $this->redirectToRoute('discipline_edit', ['id' => $discipline->getId()]);
        }
        try {
            $disciplineRepository->remove($discipline);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una propiedad si ya tiene cursos creados.');

            return $this->redirectToRoute('discipline_edit', ['id' => $discipline->getId()]);
        }

        return $this->redirectToRoute('discipline_index');
    }
}
