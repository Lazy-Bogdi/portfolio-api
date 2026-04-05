<?php

namespace App\Tests\Controller;

use App\Tests\AuthenticatedTestCase;

class AuthControllerTest extends AuthenticatedTestCase
{
    public function testLoginSuccess(): void
    {
        $this->createTestAdmin();

        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'test@admin.dev', 'password' => 'password']));

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $data);
        self::assertArrayHasKey('refresh_token', $data);
    }

    public function testLoginInvalidCredentials(): void
    {
        $this->createTestAdmin();

        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'test@admin.dev', 'password' => 'wrong']));

        self::assertResponseStatusCodeSame(401);
    }

    public function testProtectedEndpointWithoutToken(): void
    {
        $this->client->request('POST', '/api/skills', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['label' => 'Test']));

        self::assertResponseStatusCodeSame(401);
    }

    public function testProtectedEndpointWithToken(): void
    {
        $this->authenticate();

        $this->authRequest('GET', '/api/contacts');
        self::assertResponseIsSuccessful();
    }
}
