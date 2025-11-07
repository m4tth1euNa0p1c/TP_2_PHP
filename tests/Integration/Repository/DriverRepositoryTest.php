<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Driver;
use App\Entity\Team;
use App\Repository\DriverRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DriverRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Driver::class);
    }

    public function testFindByTeam_ReturnsDriversOfTeam(): void
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy([]);

        if (!$team) {
            $this->markTestSkipped('No team found in database');
        }

        $drivers = $this->repository->findByTeam($team->getId());

        foreach ($drivers as $driver) {
            $this->assertEquals($team->getId(), $driver->getTeam()->getId());
        }
    }

    public function testFindByTeam_WithIsStarterFilter_ReturnsOnlyStarters(): void
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy([]);

        if (!$team) {
            $this->markTestSkipped('No team found in database');
        }

        $drivers = $this->repository->findByTeam($team->getId(), true);

        foreach ($drivers as $driver) {
            $this->assertTrue($driver->isStarter());
        }
    }

    public function testFindActive_ReturnsOnlyActiveDrivers(): void
    {
        $drivers = $this->repository->findActive();

        foreach ($drivers as $driver) {
            $this->assertEquals(Driver::STATUS_ACTIVE, $driver->getStatus());
        }
    }

    public function testFindSuspended_ReturnsOnlySuspendedDrivers(): void
    {
        $drivers = $this->repository->findSuspended();

        foreach ($drivers as $driver) {
            $this->assertEquals(Driver::STATUS_SUSPENDED, $driver->getStatus());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
