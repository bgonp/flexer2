<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Age;
use App\Repository\AgeRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/age") */
class AgeController extends BaseController
{
    /** @Route("/", name="age_index", methods={"GET"}) */
    public function index(AgeRepository $ageRepository): Response
    {
        if (!$this->canList(Age::class)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('age/index.html.twig', ['ages' => $ageRepository->findAll()]);
    }

    /** @Route("/new", name="age_new", methods={"GET", "POST"}) */
    public function new(Request $request, AgeRepository $ageRepository): Response
    {
        if (!$this->canCreate(Age::class)) {
            return $this->redirectToRoute('age_index');
        }
        if ($request->isMethod('POST')) {
            $ageRepository->save($age = (new Age())
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'))
            );

            return $this->redirectToRoute('age_edit', ['id' => $age->getId()]);
        }

        return $this->render('age/new.html.twig');
    }

    /** @Route("/{id}", name="age_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Age $age, AgeRepository $ageRepository): Response
    {
        if (!$this->canView($age)) {
            return $this->redirectToRoute('age_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($age)) {
                return $this->redirectToRoute('age_edit', ['id' => $age->getId()]);
            }
            $ageRepository->save($age
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'))
            );
        }

        return $this->render('age/edit.html.twig', [
            'age' => $age,
            'canEdit' => $this->canEdit($age),
        ]);
    }

    /** @Route("/{id}/delete", name="age_delete", methods={"GET"}) */
    public function delete(Age $age, AgeRepository $ageRepository): Response
    {
        if (!$this->canDelete($age)) {
            return $this->redirectToRoute('age_edit', ['id' => $age->getId()]);
        }
        try {
            $ageRepository->remove($age);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una propiedad si ya tiene cursos creados.');

            return $this->redirectToRoute('age_edit', ['id' => $age->getId()]);
        }

        return $this->redirectToRoute('age_index');
    }
}
