<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Zone;
use App\Repository\ZoneRepository;
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

    /** @Route("/{id}", name="zone_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Zone $zone, ZoneRepository $zoneRepository): Response
    {
        if (!$this->canView($zone)) {
            return $this->redirectToRoute('zone_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($zone)) {
                return $this->redirectToRoute('zone_edit', ['id' => $zone->getId()]);
            }
            $zoneRepository->save($zone
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
            );
        }

        return $this->render('zone/edit.html.twig', [
            'zone' => $zone,
            'canEdit' => $this->canEdit($zone),
        ]);
    }
}
