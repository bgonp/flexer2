<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Repository\ZoneRepository;

class ZoneResolver extends BaseResolver
{
    public function __construct(ZoneRepository $repository)
    {
        parent::__construct($repository);
    }
}