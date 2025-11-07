<?php

namespace App\Tests\Unit\Service;

use App\Entity\Driver;
use App\Entity\Infraction;
use App\Entity\Team;
use App\Service\InfractionManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class InfractionManagerTest extends TestCase
{
    private $entityManager;
    private $logger;
    private $infractionManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->infractionManager = new InfractionManager(
            $this->entityManager,
            $this->logger
        );
    }

    public function testCreatePenaltyForDriver_DecrementsPoints(): void
    {
        $driver = new Driver();
        $driver->setFirstName('Test');
        $driver->setLastName('Driver');
        $driver->setLicensePoints(12);
        $driver->setStatus(Driver::STATUS_ACTIVE);

        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->entityManager->expects($this->once())
            ->method('commit');

        $this->logger->expects($this->atLeastOnce())
            ->method('info');

        $infraction = $this->infractionManager->createPenaltyForDriver(
            $driver,
            3,
            'GP Test',
            'Track limits',
            new \DateTime()
        );

        $this->assertInstanceOf(Infraction::class, $infraction);
        $this->assertEquals(9, $driver->getLicensePoints());
        $this->assertEquals(Driver::STATUS_SUSPENDED, $driver->getStatus());
    }

    public function testCreatePenaltyForDriver_SuspendsWhenBelow12(): void
    {
        $driver = new Driver();
        $driver->setFirstName('Test');
        $driver->setLastName('Driver');
        $driver->setLicensePoints(11);
        $driver->setStatus(Driver::STATUS_ACTIVE);

        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->entityManager->expects($this->once())
            ->method('commit');

        $this->logger->expects($this->atLeastOnce())
            ->method('warning');

        $this->infractionManager->createPenaltyForDriver(
            $driver,
            2,
            'GP Test',
            'Collision',
            new \DateTime()
        );

        $this->assertEquals(9, $driver->getLicensePoints());
        $this->assertEquals(Driver::STATUS_SUSPENDED, $driver->getStatus());
    }

    public function testCreateFineForTeam_DoesNotAffectDriver(): void
    {
        $team = new Team();
        $team->setName('Test Team');

        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->entityManager->expects($this->once())
            ->method('commit');

        $this->logger->expects($this->atLeastOnce())
            ->method('info');

        $infraction = $this->infractionManager->createFineForTeam(
            $team,
            50000.00,
            'GP Test',
            'Unsafe release',
            new \DateTime()
        );

        $this->assertInstanceOf(Infraction::class, $infraction);
        $this->assertEquals('FINE_EUR', $infraction->getType());
        $this->assertEquals(50000.00, (float) $infraction->getAmount());
    }

    public function testRestoreDriverPoints_ReactivatesAt12Points(): void
    {
        $driver = new Driver();
        $driver->setFirstName('Test');
        $driver->setLastName('Driver');
        $driver->setLicensePoints(10);
        $driver->setStatus(Driver::STATUS_SUSPENDED);

        $this->logger->expects($this->once())
            ->method('info');

        $this->infractionManager->restoreDriverPoints($driver, 2);

        $this->assertEquals(12, $driver->getLicensePoints());
        $this->assertEquals(Driver::STATUS_ACTIVE, $driver->getStatus());
    }
}
