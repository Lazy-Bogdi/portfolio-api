<?php

namespace App\Tests\Controller;

use App\Tests\AuthenticatedTestCase;

class ContactControllerTest extends AuthenticatedTestCase
{
    public function testSubmitContactPublic(): void
    {
        $this->client->request('POST', '/api/contact', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello, this is a test contact message.',
        ]));

        self::assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals('John Doe', $data['name']);
        self::assertFalse($data['isRead']);
    }

    public function testSubmitContactValidation(): void
    {
        $this->client->request('POST', '/api/contact', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => '',
            'email' => 'not-an-email',
            'message' => 'short',
        ]));

        self::assertResponseStatusCodeSame(422);
    }

    public function testListContactsRequiresAuth(): void
    {
        $this->client->request('GET', '/api/contacts');
        self::assertResponseStatusCodeSame(401);
    }

    public function testToggleRead(): void
    {
        $this->authenticate();

        // Create a contact first
        $this->client->request('POST', '/api/contact', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'message' => 'Another test message for toggle.',
        ]));
        $contact = json_decode($this->client->getResponse()->getContent(), true);

        // Toggle read
        $this->authRequest('PUT', '/api/contacts/'.$contact['id'].'/read');
        self::assertResponseIsSuccessful();
        $toggled = json_decode($this->client->getResponse()->getContent(), true);
        self::assertTrue($toggled['isRead']);

        // Toggle back
        $this->authRequest('PUT', '/api/contacts/'.$contact['id'].'/read');
        $toggled = json_decode($this->client->getResponse()->getContent(), true);
        self::assertFalse($toggled['isRead']);
    }
}
