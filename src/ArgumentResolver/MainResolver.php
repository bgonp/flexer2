<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Entity\Base;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Uid\Uuid;

class MainResolver implements ArgumentValueResolverInterface
{
    private ObjectManager $manager;

    private string $name;

    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager->getManager();
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $reflectionClass = new \ReflectionClass($argument->getType());
        if (!$reflectionClass->isSubclassOf(Base::class)) {
            return false;
        }
        $this->name = $argument->getName();

        return true;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        if ($id = $request->get($this->name.'_id')) {
            if (!Uuid::isValid($id)) {
                try {
                    $id = Uuid::fromString($id)->toRfc4122();
                } catch (\InvalidArgumentException $e) {
                    throw new HttpException(404, 'ID incorrecta '.$id);
                }
            }

            if (!$object = $this->manager->getRepository($argument->getType())->find($id)) {
                throw new HttpException(404, 'No existe entidad con ese ID');
            }

            yield $object;
        }
    }
}
