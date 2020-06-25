<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CustomerPosition;
use App\Entity\Position;
use App\Entity\StaffPosition;
use Doctrine\Persistence\ManagerRegistry;

// TODO: BORRAR
class PositionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    /** @return Position[] */
    public function test()
    {
        return $this->createQueryBuilder('p')
            ->where('p INSTANCE OF :position_class')
            ->setParameter('position_class', $this->getClassQueryData(CustomerPosition::class))
            ->getQuery()->execute();
    }
}
