<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    

    public function findAllWithEngine(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.engine', 'e')
            ->addSelect('e')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
