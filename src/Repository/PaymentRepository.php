<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Payment;

class PaymentRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Payment::class;
    }

    public function save(Payment $payment, bool $flush = true)
    {
        $this->saveEntity($payment, $flush);
    }
}
