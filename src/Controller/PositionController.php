<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CustomerPosition;
use App\Entity\Position;
use App\Entity\StaffPosition;
use App\Repository\CustomerPositionRepository;
use App\Repository\StaffPositionRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/position") */
class PositionController extends BaseController
{
    /** @Route("/{type<(staff|customer)>}", name="position_index", methods={"GET"}) */
    public function index(
        string $type,
        CustomerPositionRepository $customerPositionRepository,
        StaffPositionRepository $staffPositionRepository
    ): Response {
        $positions = ('customer' === $type ? $customerPositionRepository : $staffPositionRepository)->findAll();

        return $this->render('position/index.html.twig', [
            'type' => $type,
            'positions' => $positions,
        ]);
    }

    /** @Route("/{type<(staff|customer)>}/new", name="position_new", methods={"GET", "POST"}) */
    public function new(
        string $type,
        Request $request,
        CustomerPositionRepository $customerPositionRepository,
        StaffPositionRepository $staffPositionRepository
    ): Response {
        if (
            ('customer' === $type && !$this->canCreate(CustomerPosition::class)) ||
            ('staff' === $type && !$this->canCreate(StaffPosition::class))
        ) {
            return $this->redirectToRoute('position_index', ['type' => $type]);
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');

            if (!$name) {
                $this->addFlash('error', 'El campo "nombre" es obligatorio');
            } else {
                if ('staff' === $type) {
                    $staffPositionRepository->save($position = (new StaffPosition())->setName($name));
                } else {
                    $customerPositionRepository->save($position = (new CustomerPosition())->setName($name));
                }

                return $this->redirectToRoute('position_edit', ['position_id' => $position->getId()]);
            }
        }

        return $this->render('position/new.html.twig', [
            'type' => $type,
            'name' => $name ?? '',
        ]);
    }

    /** @Route("/{position_id}", name="position_edit", methods={"GET", "POST"}) */
    public function edit(
        Request $request,
        Position $position,
        CustomerPositionRepository $customerPositionRepository,
        StaffPositionRepository $staffPositionRepository
    ): Response {
        $type = $position instanceof StaffPosition ? 'staff' : 'customer';
        if (!$this->canView($position)) {
            return $this->redirectToRoute('position_index', ['type' => $type]);
        }

        if ($request->isMethod('POST')) {
            if (!$this->canEdit($position)) {
                return $this->redirectToRoute('position_edit', ['position_id' => $position->getId()]);
            }

            $name = $request->request->get('name');

            if (!$name) {
                $this->addFlash('error', 'El campo "nombre" es obligatorio');
            } else {
                if ('staff' === $type) {
                    $staffPositionRepository->save($position->setName($name));
                } else {
                    $customerPositionRepository->save($position->setName($name));
                }
            }
        }

        return $this->render('position/edit.html.twig', [
            'type' => $type,
            'position' => $position,
            'canEdit' => $this->canEdit($position),
        ]);
    }

    /** @Route("/{position_id}/delete", name="position_delete", methods={"GET"}) */
    public function delete(
        Position $position,
        CustomerPositionRepository $customerPositionRepository,
        StaffPositionRepository $staffPositionRepository
    ): Response {
        if (!$this->canDelete($position)) {
            return $this->redirectToRoute('position_edit', ['position_id' => $position->getId()]);
        }

        $type = $position instanceof StaffPosition ? 'staff' : 'customer';
        try {
            if ('staff' === $type) {
                $staffPositionRepository->remove($position);
            } else {
                $customerPositionRepository->remove($position);
            }
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar un cargo si tiene asociaciones.');

            return $this->redirectToRoute('position_edit', ['position_id' => $position->getId()]);
        }

        return $this->redirectToRoute('position_index', ['type' => $type]);
    }
}
