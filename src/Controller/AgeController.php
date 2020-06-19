<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Age;
use App\Repository\AgeRepository;
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
}
