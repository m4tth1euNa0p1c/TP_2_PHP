<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginWithBadCredentials_Returns401(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@example.com',
                'password' => 'wrong_password'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginWithGoodCredentials_Returns200AndToken(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@example.com',
                'password' => 'admin123'
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
    }

    public function testLoginUserCredentials_Returns200AndToken(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@example.com',
                'password' => 'user123'
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testCreateUser_WithValidData_Returns201(): void
    {
        $uniqueEmail = 'newuser_' . uniqid() . '@test.com';

        $this->client->request(
            'POST',
            '/api/user/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $uniqueEmail,
                'password' => 'password123'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('user', $data);
        $this->assertEquals($uniqueEmail, $data['user']['email']);
    }

    public function testCreateUser_WithDuplicateEmail_Returns409(): void
    {
        $this->client->request(
            'POST',
            '/api/user/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@example.com',
                'password' => 'password123'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }
}
