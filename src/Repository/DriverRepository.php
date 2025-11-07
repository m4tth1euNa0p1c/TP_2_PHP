<?php

namespace App\Repository;

use App\Entity\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DriverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    

    public function findByTeam(int $teamId, ?bool $isStarter = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.team = :teamId')
            ->setParameter('teamId', $teamId)
            ->orderBy('d.lastName', 'ASC');

        if ($isStarter !== null) {
            $qb->andWhere('d.isStarter = :isStarter')
               ->setParameter('isStarter', $isStarter);
        }

        return $qb->getQuery()->getResult();
    }

    

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.status = :status')
            ->setParameter('status', $status)
            ->orderBy('d.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    

    public function findActive(): array
    {
        return $this->findByStatus(Driver::STATUS_ACTIVE);
    }

    

    public function findSuspended(): array
    {
        return $this->findByStatus(Driver::STATUS_SUSPENDED);
    }
}
