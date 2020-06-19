<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Level;
use App\Repository\LevelRepository;
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

    /** @Route("/{id}", name="level_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Level $level, LevelRepository $levelRepository): Response
    {
        if (!$this->canView($level)) {
            return $this->redirectToRoute('level_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($level)) {
                return $this->redirectToRoute('level_edit', ['id' => $level->getId()]);
            }
            $levelRepository->save($level
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'))
            );
        }

        return $this->render('level/edit.html.twig', [
            'level' => $level,
            'canEdit' => $this->canEdit($level),
        ]);
    }
}
