<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Repository\ListingRepository;

class ListingResolver extends BaseResolver
{
    public function __construct(ListingRepository $repository)
    {
        parent::__construct($repository);
    }
}
