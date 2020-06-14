<?php

declare(strict_types=1);

namespace App\Exception\Common;

use App\Entity\Base;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ObjectOfClassCouldNotBeRemovedException extends BadRequestHttpException
{
    private const MESSAGE = 'Object of class %s couldn\'t be removed';

    public static function create(Base $object): self
    {
        return new self(sprintf(self::MESSAGE, get_class($object)));
    }
}
