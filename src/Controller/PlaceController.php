<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use App\Repository\ZoneRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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

    /** @Route("/new", name="place_new", methods={"GET", "POST"}) */
    public function new(Request $request, PlaceRepository $placeRepository, ZoneRepository $zoneRepository): Response
    {
        if (!$this->canCreate(Place::class)) {
            return $this->redirectToRoute('place_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            $zone = ($zoneId = $request->request->get('zone')) ? $zoneRepository->find($zoneId) : null;
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } elseif (is_null($zone)) {
                $this->addFlash('error', 'El campo "zona" es obligatorio');
            } else {
                $placeRepository->save($place = (new Place())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                    ->setZone($zone)
                );

                return $this->redirectToRoute('place_edit', ['id' => $place->getId()]);
            }
        }

        return $this->render('place/new.html.twig', [
            'zones' => $zoneRepository->findAll(),
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
            'zone' => $zone ?? null,
        ]);
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

    /** @Route("/{id}/delete", name="place_delete", methods={"GET"}) */
    public function delete(Place $place, PlaceRepository $placeRepository): Response
    {
        if (!$this->canDelete($place)) {
            return $this->redirectToRoute('place_edit', ['id' => $place->getId()]);
        }
        try {
            $placeRepository->remove($place);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una propiedad si ya tiene cursos creados.');

            return $this->redirectToRoute('place_edit', ['id' => $place->getId()]);
        }

        return $this->redirectToRoute('place_index');
    }
}
