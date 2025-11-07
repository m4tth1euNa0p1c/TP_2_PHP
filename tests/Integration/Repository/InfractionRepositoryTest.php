<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Driver;
use App\Entity\Infraction;
use App\Entity\Team;
use App\Repository\InfractionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InfractionRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Infraction::class);
    }

    public function testSearchFiltersByDriver(): void
    {
        $driver = $this->entityManager->getRepository(Driver::class)->findOneBy([]);

        if (!$driver) {
            $this->markTestSkipped('No driver found in database');
        }

        $results = $this->repository->search(
            driverId: $driver->getId()
        );

        foreach ($results as $infraction) {
            if ($infraction->getDriver()) {
                $this->assertEquals($driver->getId(), $infraction->getDriver()->getId());
            }
        }
    }

    public function testSearchFiltersByTeam(): void
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy([]);

        if (!$team) {
            $this->markTestSkipped('No team found in database');
        }

        $results = $this->repository->search(
            teamId: $team->getId()
        );

        foreach ($results as $infraction) {
            if ($infraction->getTeam()) {
                $this->assertEquals($team->getId(), $infraction->getTeam()->getId());
            }
        }
    }

    public function testSearchFiltersByDateRange(): void
    {
        $from = new \DateTime('2025-03-01');
        $to = new \DateTime('2025-03-31');

        $results = $this->repository->search(
            from: $from,
            to: $to
        );

        foreach ($results as $infraction) {
            $this->assertGreaterThanOrEqual($from, $infraction->getOccurredAt());
            $this->assertLessThanOrEqual($to, $infraction->getOccurredAt());
        }
    }

    public function testGetTotalFinesByTeam(): void
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy([]);

        if (!$team) {
            $this->markTestSkipped('No team found in database');
        }

        $total = $this->repository->getTotalFinesByTeam($team->getId());
        $this->assertIsFloat($total);
        $this->assertGreaterThanOrEqual(0, $total);
    }

    public function testGetTotalPenaltyPointsByDriver(): void
    {
        $driver = $this->entityManager->getRepository(Driver::class)->findOneBy([]);

        if (!$driver) {
            $this->markTestSkipped('No driver found in database');
        }

        $total = $this->repository->getTotalPenaltyPointsByDriver($driver->getId());
        $this->assertIsInt($total);
        $this->assertGreaterThanOrEqual(0, $total);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
