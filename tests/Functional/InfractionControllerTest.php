<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InfractionControllerTest extends WebTestCase
{
    private $client;
    private $adminToken;
    private $userToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->adminToken = $this->getToken('admin@example.com', 'admin123');
        $this->userToken = $this->getToken('user@example.com', 'user123');
    }

    private function getToken(string $email, string $password): string
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        return $data['token'] ?? '';
    }

    public function testAccessWithoutToken_Returns401(): void
    {
        $this->client->request('GET', '/api/infractions');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testListInfractions_WithValidToken_Returns200(): void
    {
        $this->client->request(
            'GET',
            '/api/infractions',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
    }

    public function testCreateInfraction_AsUser_Returns403(): void
    {
        $this->client->request(
            'POST',
            '/api/infractions',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'type' => 'FINE_EUR',
                'amount' => 1000,
                'teamId' => 1,
                'raceName' => 'GP Test',
                'occurredAt' => '2025-07-01T10:00:00Z',
                'description' => 'Test'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreatePenalty_AsAdmin_DecrementsPoints(): void
    {
        
        $this->client->request(
            'GET',
            '/api/driver/1',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );
        $driverBefore = json_decode($this->client->getResponse()->getContent(), true);
        $pointsBefore = $driverBefore['data']['licensePoints'];

        
        $this->client->request(
            'POST',
            '/api/infractions',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'type' => 'PENALTY_POINTS',
                'amount' => 3,
                'driverId' => 1,
                'raceName' => 'GP Test Penalty',
                'occurredAt' => '2025-07-11T14:00:00Z',
                'description' => 'Dépassement des limites de piste'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('driver', $data);
        $expectedPoints = max(0, $pointsBefore - 3);
        $this->assertEquals($expectedPoints, $data['driver']['licensePoints']);

        
        if ($expectedPoints < 12) {
            $this->assertEquals('suspendu', $data['driver']['status']);
        }
    }

    public function testCreateFine_AsAdmin_OnTeam_Returns201(): void
    {
        $this->client->request(
            'POST',
            '/api/infractions',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'type' => 'FINE_EUR',
                'amount' => 50000.00,
                'teamId' => 1,
                'raceName' => 'GP Test Fine',
                'occurredAt' => '2025-07-11T16:00:00Z',
                'description' => 'Sortie dangereuse des stands'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('infraction', $data);
        $this->assertEquals('FINE_EUR', $data['infraction']['type']);
        $this->assertArrayHasKey('team', $data);
    }

    public function testCreateInfraction_WithBothDriverAndTeam_Returns400(): void
    {
        $this->client->request(
            'POST',
            '/api/infractions',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'type' => 'FINE_EUR',
                'amount' => 1000,
                'driverId' => 1,
                'teamId' => 1,
                'raceName' => 'GP Test',
                'occurredAt' => '2025-07-01T10:00:00Z',
                'description' => 'Test'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('MULTIPLE_TARGETS', $data['code']);
    }

    public function testCreateInfraction_WithNeitherDriverNorTeam_Returns400(): void
    {
        $this->client->request(
            'POST',
            '/api/infractions',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'type' => 'FINE_EUR',
                'amount' => 1000,
                'raceName' => 'GP Test',
                'occurredAt' => '2025-07-01T10:00:00Z',
                'description' => 'Test'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('TARGET_REQUIRED', $data['code']);
    }

    public function testListInfractions_WithDriverFilter_ReturnsOnlyDriver(): void
    {
        $this->client->request(
            'GET',
            '/api/infractions?driverId=1',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        foreach ($data['data'] as $infraction) {
            if (isset($infraction['driver'])) {
                $this->assertEquals(1, $infraction['driver']['id']);
            }
        }
    }

    public function testListInfractions_WithTeamFilter_ReturnsOnlyTeam(): void
    {
        $this->client->request(
            'GET',
            '/api/infractions?teamId=1',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        foreach ($data['data'] as $infraction) {
            if (isset($infraction['team'])) {
                $this->assertEquals(1, $infraction['team']['id']);
            }
        }
    }

    public function testListInfractions_WithDateInterval_ReturnsInsideBounds(): void
    {
        $this->client->request(
            'GET',
            '/api/infractions?from=2025-03-01&to=2025-03-31',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        foreach ($data['data'] as $infraction) {
            $date = new \DateTime($infraction['occurredAt']);
            $this->assertGreaterThanOrEqual(new \DateTime('2025-03-01'), $date);
            $this->assertLessThanOrEqual(new \DateTime('2025-03-31'), $date);
        }
    }
}
