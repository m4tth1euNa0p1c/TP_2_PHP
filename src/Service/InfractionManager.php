<?php

namespace App\Service;

use App\Entity\Driver;
use App\Entity\Infraction;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class InfractionManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }

    

    public function createPenaltyForDriver(
        Driver $driver,
        int $points,
        string $raceName,
        string $description,
        \DateTimeInterface $occurredAt
    ): Infraction {
        $this->entityManager->beginTransaction();

        try {
            
            $infraction = new Infraction();
            $infraction->setType(Infraction::TYPE_PENALTY_POINTS);
            $infraction->setAmount((string) $points);
            $infraction->setDriver($driver);
            $infraction->setRaceName($raceName);
            $infraction->setDescription($description);
            $infraction->setOccurredAt($occurredAt);

            
            $currentPoints = $driver->getLicensePoints();
            $newPoints = max(0, $currentPoints - $points);
            $driver->setLicensePoints($newPoints);

            $this->logger->info('Pénalité en points appliquée', [
                'driver' => $driver->getFullName(),
                'points_retirés' => $points,
                'points_avant' => $currentPoints,
                'points_après' => $newPoints,
                'race' => $raceName
            ]);

            
            if ($newPoints < 12 && $driver->getStatus() !== Driver::STATUS_SUSPENDED) {
                $driver->setStatus(Driver::STATUS_SUSPENDED);

                $this->logger->warning('Pilote suspendu automatiquement', [
                    'driver' => $driver->getFullName(),
                    'points_restants' => $newPoints
                ]);
            }

            $this->entityManager->persist($infraction);
            $this->entityManager->persist($driver);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $infraction;

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Erreur lors de la création de la pénalité', [
                'driver' => $driver->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    

    public function createFineForTeam(
        Team $team,
        float $amount,
        string $raceName,
        string $description,
        \DateTimeInterface $occurredAt
    ): Infraction {
        $this->entityManager->beginTransaction();

        try {
            $infraction = new Infraction();
            $infraction->setType(Infraction::TYPE_FINE_EUR);
            $infraction->setAmount((string) $amount);
            $infraction->setTeam($team);
            $infraction->setRaceName($raceName);
            $infraction->setDescription($description);
            $infraction->setOccurredAt($occurredAt);

            $this->logger->info('Amende appliquée à une écurie', [
                'team' => $team->getName(),
                'montant' => $amount,
                'race' => $raceName
            ]);

            $this->entityManager->persist($infraction);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $infraction;

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Erreur lors de la création de l\'amende', [
                'team' => $team->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    

    public function restoreDriverPoints(Driver $driver, int $points): void
    {
        $currentPoints = $driver->getLicensePoints();
        $newPoints = min(12, $currentPoints + $points);
        $driver->setLicensePoints($newPoints);

        
        if ($newPoints >= 12 && $driver->getStatus() === Driver::STATUS_SUSPENDED) {
            $driver->setStatus(Driver::STATUS_ACTIVE);
            $this->logger->info('Pilote réactivé', [
                'driver' => $driver->getFullName(),
                'points' => $newPoints
            ]);
        }

        $this->entityManager->persist($driver);
        $this->entityManager->flush();
    }
}
