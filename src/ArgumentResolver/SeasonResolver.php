<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Repository\SeasonRepository;

class SeasonResolver extends BaseResolver
{
    public function __construct(SeasonRepository $repository)
    {
        parent::__construct($repository);
    }
}
