<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Base;
use App\Entity\Customer;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    protected function canList(string $entityClass, bool $flash = true): bool
    {
        if ($this->isGranted(Role::ROLE_ADMIN)) {
            return true;
        }
        if ($flash) {
            $this->addFlash('error', 'Acceso restringido');
        }

        return false;
    }

    protected function canView(Base $entity, bool $flash = true): bool
    {
        if ($this->isGranted(Role::ROLE_ADMIN)) {
            return true;
        }
        if (Customer::class === get_class($entity)) {
            return $this->getUser() && $this->getUser()->equals($entity->getUser());
        }
        if ($flash) {
            $this->addFlash('error', 'Acceso restringido');
        }

        return false;
    }

    protected function canEdit(Base $entity, bool $flash = true): bool
    {
        if ($this->isGranted(Role::ROLE_ADMIN)) {
            return true;
        }
        if (Customer::class === get_class($entity)) {
            return $this->getUser() && $this->getUser()->equals($entity->getUser());
        }
        if ($flash) {
            $this->addFlash('error', 'Acceso restringido');
        }

        return false;
    }
}
