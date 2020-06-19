<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Place;
use App\Entity\Zone;
use App\Repository\PlaceRepository;
use App\Repository\ZoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/place") */
class PlaceController extends BaseController
{
    /** @Route("/", name="place_index", methods={"GET"}) */
    public function index(PlaceRepository $placeRepository): Response
    {
        if (!$this->canList(Place::class)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('place/index.html.twig', ['places' => $placeRepository->findAll()]);
    }

    /** @Route("/{id}", name="place_edit", methods={"GET", "POST"}) */
    public function edit(
        Request $request,
        Place $place,
        PlaceRepository $placeRepository,
        ZoneRepository $zoneRepository
    ): Response {
        if (!$this->canView($place)) {
            return $this->redirectToRoute('place_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($place)) {
                return $this->redirectToRoute('place_edit', ['id' => $place->getId()]);
            }
            $placeRepository->save($place
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'))
                ->setZone($zoneRepository->find($request->request->get('zone')))
            );
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'zones' => $zoneRepository->findAll(),
            'canEdit' => $this->canEdit($place),
        ]);
    }
}
