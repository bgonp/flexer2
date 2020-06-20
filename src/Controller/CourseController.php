<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/course") */
class CourseController extends BaseController
{
    /** @Route("/{page<\d+>}", name="course_index", methods={"GET"}) */
    public function index(Request $request, CourseRepository $courseRepository, int $page = 1): Response
    {
        if (!$this->canList(Course::class)) {
            return $this->redirectToRoute('main');
        }
        if ($request->query->has('s')) {
            if (!$search = $request->query->get('s')) {
                return $this->redirectToRoute('course_index');
            }
            if ($page > $lastPage = $courseRepository->getLastPageBySearchTerm($search)) {
                return $this->redirectToRoute('course_index', ['s' => $search, 'page' => $lastPage]);
            }
            $courses = $courseRepository->findBySearchTerm($search, $page);
        } else {
            if ($page > $lastPage = $courseRepository->getLastPage()) {
                return $this->redirectToRoute('course_index', ['page' => $lastPage]);
            }
            $courses = $courseRepository->findAll($page);
        }

        return $this->render('course/index.html.twig', [
            'search' => $search ?? '',
            'courses' => $courses,
            'currentPage' => $page,
            'lastPage' => $lastPage,
        ]);
    }

    /** @Route("/new", name="course_new", methods={"GET", "POST"}) */
    public function new(Request $request, CourseRepository $courseRepository): Response
    {
        if (!$this->canCreate(Course::class)) {
            return $this->redirectToRoute('course_index');
        }
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            if (empty($name)) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $courseRepository->save($course = (new Course())
                    ->setName($name)
                    ->setDescription($description)
                    ->setUrl($url)
                );

                return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
            }
        }

        return $this->render('course/new.html.twig', [
            'name' => $name ?? '',
            'description' => $description ?? '',
            'url' => $url ?? '',
        ]);
    }

    /** @Route("/{id}", name="course_edit", methods={"GET", "POST"}) */
    public function edit(Request $request, Course $course, CourseRepository $courseRepository): Response
    {
        if (!$this->canView($course)) {
            return $this->redirectToRoute('course_index');
        }
        if ($request->isMethod('POST')) {
            if (!$this->canEdit($course)) {
                return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
            }
            $course
                ->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setUrl($request->request->get('url'));
            if (empty($course->getName())) {
                $this->addFlash('error', 'El campo "nombre" no puede estar vacío');
            } else {
                $courseRepository->save($course);
            }
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
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

        return $this->redirectToRoute('course_index');
    }
}
