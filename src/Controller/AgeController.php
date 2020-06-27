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
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $ageRepository->save($age = (new Age())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                );

                return $this->redirectToRoute('age_edit', ['age_id' => $age->getId()]);
            }
        }

        return $this->render('age/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
        ]);
    }

    /** @Route("/{age_id}", name="age_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Age $age, AgeRepository $ageRepository): Response
    {
        if (!$this->canView($age)) {
            return $this->redirectToRoute('age_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($age)) {
                return $this->redirectToRoute('age_edit', ['age_id' => $age->getId()]);
            }
            $age
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'));
            if (empty($age->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $ageRepository->save($age);
            }
        }

        return $this->render('age/edit.html.twig', [
            'age' => $age,
            'canEdit' => $this->canEdit($age),
        ]);
    }

    /** @Route("/{age_id}/delete", name="age_delete", methods={"GET"}) */
    public function delete(Age $age, AgeRepository $ageRepository): Response
    {
        if (!$this->canDelete($age)) {
            return $this->redirectToRoute('age_edit', ['age_id' => $age->getId()]);
        }
        try {
            $ageRepository->remove($age);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una propiedad si ya tiene cursos creados.');

            return $this->redirectToRoute('age_edit', ['age_id' => $age->getId()]);
        }

        return $this->redirectToRoute('age_index');
    }
}
