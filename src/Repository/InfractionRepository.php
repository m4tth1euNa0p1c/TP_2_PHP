<?php

namespace App\Repository;

use App\Entity\Infraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InfractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infraction::class);
    }

    

    public function search(
        ?int $teamId = null,
        ?int $driverId = null,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): array {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.driver', 'd')
            ->leftJoin('i.team', 't')
            ->addSelect('d', 't');

        if ($teamId !== null) {
            $qb->andWhere('i.team = :teamId')
               ->setParameter('teamId', $teamId);
        }

        if ($driverId !== null) {
            $qb->andWhere('i.driver = :driverId')
               ->setParameter('driverId', $driverId);
        }

        if ($from !== null) {
            $qb->andWhere('i.occurredAt >= :from')
               ->setParameter('from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('i.occurredAt <= :to')
               ->setParameter('to', $to);
        }

        $qb->orderBy('i.occurredAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    

    public function getTotalFinesByTeam(int $teamId): float
    {
        $result = $this->createQueryBuilder('i')
            ->select('SUM(i.amount)')
            ->where('i.team = :teamId')
            ->andWhere('i.type = :type')
            ->setParameter('teamId', $teamId)
            ->setParameter('type', Infraction::TYPE_FINE_EUR)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    

    public function getTotalPenaltyPointsByDriver(int $driverId): int
    {
        $result = $this->createQueryBuilder('i')
            ->select('SUM(i.amount)')
            ->where('i.driver = :driverId')
            ->andWhere('i.type = :type')
            ->setParameter('driverId', $driverId)
            ->setParameter('type', Infraction::TYPE_PENALTY_POINTS)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }
}
