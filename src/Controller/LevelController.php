<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Level;
use App\Repository\LevelRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/level") */
class LevelController extends BaseController
{
    /** @Route("/", name="level_index", methods={"GET"}) */
    public function index(LevelRepository $levelRepository): Response
    {
        if (!$this->canList(Level::class)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('level/index.html.twig', ['levels' => $levelRepository->findAll()]);
    }

    /** @Route("/new", name="level_new", methods={"GET", "POST"}) */
    public function new(Request $request, LevelRepository $levelRepository): Response
    {
        if (!$this->canCreate(Level::class)) {
            return $this->redirectToRoute('level_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $levelRepository->save($level = (new Level())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                );

                return $this->redirectToRoute('level_edit', ['level_id' => $level->getId()]);
            }
        }

        return $this->render('level/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
        ]);
    }

    /** @Route("/{level_id}", name="level_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Level $level, LevelRepository $levelRepository): Response
    {
        if (!$this->canView($level)) {
            return $this->redirectToRoute('level_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($level)) {
                return $this->redirectToRoute('level_edit', ['level_id' => $level->getId()]);
            }
            $level
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'));
            if (empty($level->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $levelRepository->save($level);
            }
        }

        return $this->render('level/edit.html.twig', [
            'level' => $level,
            'canEdit' => $this->canEdit($level),
        ]);
    }

    /** @Route("/{level_id}/delete", name="level_delete", methods={"GET"}) */
    public function delete(Level $level, LevelRepository $levelRepository): Response
    {
        if (!$this->canDelete($level)) {
            return $this->redirectToRoute('level_edit', ['level_id' => $level->getId()]);
        }
        try {
            $levelRepository->remove($level);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una propiedad si ya tiene cursos creados.');

            return $this->redirectToRoute('level_edit', ['level_id' => $level->getId()]);
        }

        return $this->redirectToRoute('level_index');
    }
}
