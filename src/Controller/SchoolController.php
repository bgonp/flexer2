<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\School;
use App\Repository\SchoolRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/school") */
class SchoolController extends BaseController
{
    /** @Route("/", name="school_index", methods={"GET"}) */
    public function index(SchoolRepository $schoolRepository): Response
    {
        if (!$this->canList(School::class)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('school/index.html.twig', ['schools' => $schoolRepository->findAll()]);
    }

    /** @Route("/new", name="school_new", methods={"GET", "POST"}) */
    public function new(Request $request, SchoolRepository $schoolRepository): Response
    {
        if (!$this->canCreate(School::class)) {
            return $this->redirectToRoute('school_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $schoolRepository->save($school = (new School())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                );

                return $this->redirectToRoute('school_edit', ['id' => $school->getId()]);
            }
        }

        return $this->render('school/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
        ]);
    }

    /** @Route("/{id}", name="school_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, School $school, SchoolRepository $schoolRepository): Response
    {
        if (!$this->canView($school)) {
            return $this->redirectToRoute('school_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($school)) {
                return $this->redirectToRoute('school_edit', ['id' => $school->getId()]);
            }
            $school
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'));
            if (empty($school->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $schoolRepository->save($school);
            }
        }

        return $this->render('school/edit.html.twig', [
            'school' => $school,
            'canEdit' => $this->canEdit($school),
        ]);
    }

    /** @Route("/{id}/delete", name="school_delete", methods={"GET"}) */
    public function delete(School $school, SchoolRepository $schoolRepository): Response
    {
        if (!$this->canDelete($school)) {
            return $this->redirectToRoute('school_edit', ['id' => $school->getId()]);
        }
        try {
            $schoolRepository->remove($school);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una escuela si tiene cursos asociados.');

            return $this->redirectToRoute('school_edit', ['id' => $school->getId()]);
        }

        return $this->redirectToRoute('school_index');
    }
}
