<?php

declare(strict_types=1);

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ObjectOfClassNotSupportedException extends BadRequestHttpException
{
    private const MESSAGE = 'Object of class %s not supported.';

    public static function create(object $object): self
    {
        return new self(sprintf(self::MESSAGE, get_class($object)));
    }
}
