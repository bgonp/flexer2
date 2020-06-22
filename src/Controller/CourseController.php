<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Course;
use App\Exception\Common\PageOutOfBoundsException;
use App\Repository\AgeRepository;
use App\Repository\CourseRepository;
use App\Repository\DisciplineRepository;
use App\Repository\LevelRepository;
use App\Repository\PlaceRepository;
use App\Repository\SchoolRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/** @Route("/course") */
class CourseController extends BaseController
{
    /** @Route("/{type<(all|active|inactive)>}/{page<[1-9]\d*>}", name="course_index", methods={"GET"}) */
    public function index(Request $request, CourseRepository $courseRepository, string $type, int $page = 1): Response
    {
        if (!$this->canList(Course::class)) {
            return $this->redirectToRoute('main');
        }

        if ($request->query->has('s')) {
            if (!$search = $request->query->get('s')) {
                return $this->redirectToRoute('course_index', ['type' => $type]);
            }

            try {
                $courses = $courseRepository->findBySearchTermPaged($search, $type, $page);
            } catch (PageOutOfBoundsException $e) {
                return $this->redirectToRoute('course_index', ['type' => $type, 's' => $search]);
            }
        } else {
            try {
                $courses = $courseRepository->findAllPaged($type, $page);
            } catch (PageOutOfBoundsException $e) {
                return $this->redirectToRoute('course_index', ['type' => $type]);
            }
        }

        return $this->render('course/index.html.twig', [
            'search' => $search ?? '',
            'courses' => $courses->getResults(),
            'currentPage' => $courses->getPage(),
            'lastPage' => $courses->getLastPage(),
        ]);
    }

    /** @Route("/new", name="course_new", methods={"GET", "POST"}) */
    public function new(
        Request $request,
        CourseRepository $courseRepository,
        SchoolRepository $schoolRepository,
        PlaceRepository $placeRepository,
        DisciplineRepository $disciplineRepository,
        LevelRepository $levelRepository,
        AgeRepository $ageRepository
    ): Response {
        if (!$this->canCreate(Course::class)) {
            return $this->redirectToRoute('course_index', ['type' => 'active']);
        }
        if ($request->isMethod('POST')) {
            $dayOfWeek = [(int) $request->request->get('dayOfWeek')];
            $time = explode(':', $request->request->get('time'));
            $school = $schoolRepository->find($request->request->get('school'));
            $place = $placeRepository->find($request->request->get('place'));
            $discipline = $disciplineRepository->find($request->request->get('discipline'));
            $level = $levelRepository->find($request->request->get('level'));
            $age = $ageRepository->find($request->request->get('age'));
            $active = (bool) $request->request->get('active');

            if (!$school) {
                $this->addFlash('error', 'El campo "escuela" es obligatorio');
            } elseif (empty($time)) {
                $this->addFlash('error', 'El campo "hora" es obligatorio');
            } elseif (!$dayOfWeek[0]) {
                $this->addFlash('error', 'El campo "día" es obligatorio');
            } else {
                $datetime = (new \DateTime())->setTime((int) $time[0], (int) $time[1]);
                $courseRepository->save($course = (new Course())
                    ->setDayOfWeek($dayOfWeek)
                    ->setTime($datetime)
                    ->setSchool($school)
                    ->setPlace($place)
                    ->setDiscipline($discipline)
                    ->setLevel($level)
                    ->setAge($age)
                    ->setIsActive($active)
                );

                return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
            }
        }

        return $this->render('course/new.html.twig', [
            'dayOfWeek' => $dayOfWeek[0] ?? null,
            'time' => $datetime ?? null,
            'school' => $school ?? null,
            'place' => $place ?? null,
            'discipline' => $discipline ?? null,
            'level' => $level ?? null,
            'age' => $age ?? null,
            'active' => $isActive ?? true,
            'schools' => $schoolRepository->findAll(),
            'places' => $placeRepository->findAll(),
            'disciplines' => $disciplineRepository->findAll(),
            'levels' => $levelRepository->findAll(),
            'ages' => $ageRepository->findAll(),
        ]);
    }

    /** @Route("/{id}", name="course_edit", methods={"GET", "POST"}) */
    public function edit(
        Request $request,
        Course $course,
        CourseRepository $courseRepository,
        SchoolRepository $schoolRepository,
        PlaceRepository $placeRepository,
        DisciplineRepository $disciplineRepository,
        LevelRepository $levelRepository,
        AgeRepository $ageRepository
    ): Response {
        if (!$this->canView($course)) {
            return $this->redirectToRoute('course_index', ['type' => 'active']);
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($course)) {
                return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
            }
            $course
                ->setPlace($placeRepository->find($request->request->get('place')))
                ->setDiscipline($disciplineRepository->find($request->request->get('discipline')))
                ->setLevel($levelRepository->find($request->request->get('level')))
                ->setAge($ageRepository->find($request->request->get('age')))
                ->setIsActive((bool) $request->request->get('active'));
            if (!$school = $schoolRepository->find($request->request->get('school'))) {
                $this->addFlash('error', 'El campo "escuela" es obligatorio');
            } elseif (empty($time = explode(':', $request->request->get('time')))) {
                $this->addFlash('error', 'El campo "hora" es obligatorio');
            } elseif (!$dayOfWeek = $request->request->get('dayOfWeek')) {
                $this->addFlash('error', 'El campo "día" es obligatorio');
            } else {
                $courseRepository->save($course
                    ->setSchool($school)
                    ->setTime((new \DateTime())->setTime((int) $time[0], (int) $time[1]))
                    ->setDayOfWeek([$dayOfWeek])
                );
            }
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'schools' => $schoolRepository->findAll(),
            'places' => $placeRepository->findAll(),
            'disciplines' => $disciplineRepository->findAll(),
            'levels' => $levelRepository->findAll(),
            'ages' => $ageRepository->findAll(),
            'canEdit' => $this->canEdit($course),
        ]);
    }

    /** @Route("/{id}/delete", name="course_delete", methods={"GET"}) */
    public function delete(Course $course, CourseRepository $courseRepository): Response
    {
        if (!$this->canDelete($course)) {
            return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
        }
        try {
            $courseRepository->remove($course);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'No se puede eliminar una escuela si tiene cursos asociados.');

            return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
        }

        return $this->redirectToRoute('course_index', ['type' => 'active']);
    }
}
