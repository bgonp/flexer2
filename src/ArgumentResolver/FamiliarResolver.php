<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Repository\CustomerRepository;

class FamiliarResolver extends BaseResolver
{
    public function __construct(CustomerRepository $repository)
    {
        parent::__construct($repository, 'familiar');
    }
}
