<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TeamControllerTest extends WebTestCase
{
    private $client;
    private $adminToken;
    private $managerToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->adminToken = $this->getToken('admin@example.com', 'admin123');
        $this->managerToken = $this->getToken('manager@example.com', 'manager123');
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

    public function testListTeams_Returns200(): void
    {
        $this->client->request(
            'GET',
            '/api/team',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertGreaterThanOrEqual(3, $data['count']);
    }

    public function testGetTeamDrivers_Returns200(): void
    {
        $this->client->request(
            'GET',
            '/api/team/1/drivers',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('drivers', $data);
        $this->assertArrayHasKey('team', $data);
    }

    public function testGetTeamDrivers_WithStarterFilter_ReturnsOnlyStarters(): void
    {
        $this->client->request(
            'GET',
            '/api/team/1/drivers?isStarter=true',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        foreach ($data['drivers'] as $driver) {
            $this->assertTrue($driver['isStarter']);
        }
    }

    public function testPatchDrivers_AsManager_Returns200(): void
    {
        
        $this->client->request(
            'PATCH',
            '/api/team/1/drivers',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->managerToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'add' => [],
                'remove' => []
            ])
        );

        $this->assertResponseIsSuccessful();
    }

    public function testPatchDrivers_WithUnknownDriverId_Returns404(): void
    {
        $this->client->request(
            'PATCH',
            '/api/team/1/drivers',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'add' => [9999],
                'remove' => []
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testPatchDrivers_WithUnknownTeamId_Returns404(): void
    {
        $this->client->request(
            'PATCH',
            '/api/team/9999/drivers',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'add' => [],
                'remove' => []
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testPatchDrivers_WithInvalidJson_Returns400(): void
    {
        $this->client->request(
            'PATCH',
            '/api/team/1/drivers',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json'
            ],
            'invalid json'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
