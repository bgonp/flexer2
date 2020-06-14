<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Course;
use App\Entity\Customer;
use App\Entity\Named;
use App\Exception\Common\ObjectOfClassNotSupportedException;
use App\Service\CourseService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FullNameExtension extends AbstractExtension
{
    private CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function getFilters()
    {
        return [new TwigFilter('fullname', [$this, 'getFullName'])];
    }

    public function getFullName(object $object): string
    {
        if ($object instanceof Customer) {
            return "{$object->getName()} {$object->getSurname()}";
        }

        if ($object instanceof Course) {
            return $this->courseService->getFullName($object);
        }

        if ($object instanceof Named) {
            return $object->getName();
        }

        throw ObjectOfClassNotSupportedException::create($object);
    }
}
