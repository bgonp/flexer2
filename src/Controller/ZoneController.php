<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Zone;
use App\Repository\ZoneRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/zone") */
class ZoneController extends BaseController
{
    /** @Route("/", name="zone_index", methods={"GET"}) */
    public function index(ZoneRepository $zoneRepository): Response
    {
        if (!$this->canList(Zone::class)) {
            return $this->redirectToRoute('main');
        }

        return $this->render('zone/index.html.twig', ['zones' => $zoneRepository->findAll()]);
    }

    /** @Route("/new", name="zone_new", methods={"GET", "POST"}) */
    public function new(Request $request, ZoneRepository $zoneRepository): Response
    {
        if (!$this->canCreate(Zone::class)) {
            return $this->redirectToRoute('zone_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $zoneRepository->save($zone = (new Zone())
                    ->setName($name)
                    ->setDescription($description)
                );

                return $this->redirectToRoute('zone_edit', ['zone_id' => $zone->getId()]);
            }
        }

        return $this->render('zone/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
        ]);
    }

    /** @Route("/{zone_id}", name="zone_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Zone $zone, ZoneRepository $zoneRepository): Response
    {
        if (!$this->canView($zone)) {
            return $this->redirectToRoute('zone_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($zone)) {
                return $this->redirectToRoute('zone_edit', ['zone_id' => $zone->getId()]);
            }
            $zone
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'));
            if (empty($zone->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $zoneRepository->save($zone);
            }
        }

        return $this->render('zone/edit.html.twig', [
            'zone' => $zone,
            'canEdit' => $this->canEdit($zone),
        ]);
    }

    /** @Route("/{zone_id}/delete", name="zone_delete", methods={"GET"}) */
    public function delete(Zone $zone, ZoneRepository $zoneRepository): Response
    {
        if (!$this->canDelete($zone)) {
            return $this->redirectToRoute('zone_edit', ['zone_id' => $zone->getId()]);
        }
        try {
            $zoneRepository->remove($zone);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una zona si tiene lugares asociados.');

            return $this->redirectToRoute('zone_edit', ['zone_id' => $zone->getId()]);
        }

        return $this->redirectToRoute('zone_index');
    }
}
