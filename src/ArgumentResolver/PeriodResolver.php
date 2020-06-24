<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Repository\PeriodRepository;

class PeriodResolver extends BaseResolver
{
    public function __construct(PeriodRepository $repository)
    {
        parent::__construct($repository);
    }
}
