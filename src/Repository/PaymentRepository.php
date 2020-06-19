<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $payment, bool $flush = true)
    {
        $this->saveEntity($payment, $flush);
    }
}
