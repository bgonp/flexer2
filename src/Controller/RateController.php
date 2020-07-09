<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rate;
use App\Exception\Common\MissingRequiredFieldsException;
use App\Repository\RateRepository;
use App\Repository\SchoolRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/rate") */
class RateController extends BaseController
{
    /** @Route("/", name="rate_index", methods={"GET"}) */
    public function index(
        Request $request,
        RateRepository $rateRepository,
        SchoolRepository $schoolRepository
    ): Response {
        if (!$this->canList(Rate::class)) {
            return $this->redirectToRoute('main');
        }

        $school = null;
        if ($request->query->has('s')) {
            if ((!$schoolId = $request->query->get('s')) || (!$school = $schoolRepository->find($schoolId))) {
                return $this->redirectToRoute('rate_index');
            }
        }

        return $this->render('rate/index.html.twig', [
            'school' => $school ?? null,
            'rates' => $rateRepository->findBySchool($school),
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    /** @Route("/new", name="rate_new", methods={"GET", "POST"}) */
    public function new(Request $request, RateRepository $rateRepository, SchoolRepository $schoolRepository): Response
    {
        if (!$this->canCreate(Rate::class)) {
            return $this->redirectToRoute('rate_index');
        }

        $data = $this->getRateData($request);
        if ($request->isMethod('POST')) {
            try {
                $this->checkRateData($data);

                $rateRepository->save($rate
                    ->setSchool($schoolRepository->find($data['schoolId']))
                    ->setName($data['name'])
                    ->setAmount($data['amount'])
                    ->setAvailable($data['available'])
                    ->setSpreadable($data['spreadable'])
                    ->setCustomersCount($data['customersCount'])
                    ->setPeriodsCount($data['periodsCount'])
                    ->setCoursesCount($data['coursesCount'])
                );

                return $this->redirectToRoute('rate_edit', ['rate_id' => $rate->getId()]);
            } catch (MissingRequiredFieldsException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('rate/new.html.twig', [
            'school' => $data['schoolId'] ? $schoolRepository->find($data['schoolId']) : null,
            'name' => $data['name'],
            'amount' => $data['amount'],
            'available' => $data['available'],
            'spreadable' => $data['spreadable'],
            'customersCount' => $data['customersCount'],
            'periodsCount' => $data['periodsCount'],
            'coursesCount' => $data['coursesCount'],
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    /** @Route("/{rate_id}", name="rate_edit", methods={"GET", "POST"}) */
    public function edit(
        Request $request,
        Rate $rate,
        RateRepository $rateRepository,
        SchoolRepository $schoolRepository
    ): Response {
        if (!$this->canView($rate)) {
            return $this->redirectToRoute('rate_index');
        }

        $data = $this->getRateData($request);
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($rate)) {
                return $this->redirectToRoute('rate_edit', ['rate_id' => $rate->getId()]);
            }

            $rate
                ->setSchool($schoolRepository->find($data['schoolId']))
                ->setName($data['name'])
                ->setAmount($data['amount'])
                ->setAvailable($data['available'])
                ->setSpreadable($data['spreadable'])
                ->setCustomersCount($data['customersCount'])
                ->setPeriodsCount($data['periodsCount'])
                ->setCoursesCount($data['coursesCount']);

            try {
                $this->checkRateData($data);
                $rateRepository->save($rate);
            } catch (MissingRequiredFieldsException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('rate/edit.html.twig', [
            'rate' => $rate,
            'schools' => $schoolRepository->findAll(),
            'canEdit' => $this->canEdit($rate),
        ]);
    }

    /** @Route("/{rate_id}/delete", name="rate_delete", methods={"GET"}) */
    public function delete(Rate $rate, RateRepository $rateRepository): Response
    {
        if (!$this->canDelete($rate)) {
            return $this->redirectToRoute('rate_edit', ['rate_id' => $rate->getId()]);
        }

        try {
            $rateRepository->remove($rate);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una tarifa si ya se han hecho pagos con ésta.');

            return $this->redirectToRoute('rate_edit', ['rate_id' => $rate->getId()]);
        }

        return $this->redirectToRoute('rate_index');
    }

    // TODO: Hacer esta lógica en el resto de controllers
    private function getRateData(Request $request): array
    {
        return [
            'schoolId' => $request->request->has('school') ? $request->request->get('school') : null,
            'name' => $request->request->has('name') ? $request->request->get('name') : null,
            'amount' => $request->request->has('amount') ? (float) $request->request->get('amount') : null,
            'available' => $request->request->has('available') ? (bool) $request->request->get('available') : true,
            'spreadable' => $request->request->has('spreadable') ? (bool) $request->request->get('spreadable') : false,
            'customersCount' => $request->request->has('customersCount') ? (int) $request->request->get('customersCount') : 1,
            'periodsCount' => $request->request->has('periodsCount') ? (int) $request->request->get('periodsCount') : 1,
            'coursesCount' => $request->request->has('coursesCount') ? (int) $request->request->get('coursesCount') : 1,
        ];
    }

    // TODO: Hacer esta lógica en el resto de controllers
    /** @throws MissingRequiredFieldsException */
    private function checkRateData(array $data): void
    {
        $missingFields = [];

        if (!$data['schoolId']) {
            $missingFields[] = 'escuela';
        }
        if (!$data['name']) {
            $missingFields[] = 'nombre';
        }
        if (!$data['amount'] || $data['amount'] < 0) {
            $missingFields[] = 'precio';
        }
        if (!$data['customersCount'] || $data['customersCount'] < 0) {
            $missingFields[] = 'nº de alumnos';
        }
        if (!$data['periodsCount'] || $data['periodsCount'] < 0) {
            $missingFields[] = 'nº de periodos';
        }
        if (!$data['coursesCount'] || $data['coursesCount'] < 0) {
            $missingFields[] = 'nº de cursos';
        }

        if (!empty($missingFields)) {
            throw MissingRequiredFieldsException::create($missingFields);
        }
    }
}
