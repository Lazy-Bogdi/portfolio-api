<?php

namespace App\Tests\Controller;

use App\Tests\AuthenticatedTestCase;

class ProjectControllerTest extends AuthenticatedTestCase
{
    public function testListProjectsPublic(): void
    {
        $this->client->request('GET', '/api/projects');
        self::assertResponseIsSuccessful();
    }

    public function testCreateProjectRequiresAuth(): void
    {
        $this->client->request('POST', '/api/projects', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Test']));

        self::assertResponseStatusCodeSame(401);
    }

    public function testCrudProject(): void
    {
        $this->authenticate();

        // Create
        $this->authRequest('POST', '/api/projects', json_encode([
            'title' => 'Test Project',
            'shortDescription' => 'Short desc',
            'longDescription' => 'Long description here',
            'stack' => ['PHP', 'Symfony'],
            'category' => 'fullstack',
            'featured' => true,
        ]));
        self::assertResponseStatusCodeSame(201);
        $created = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals('Test Project', $created['title']);

        // Detail
        $this->client->request('GET', '/api/projects/'.$created['id']);
        self::assertResponseIsSuccessful();
        $detail = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('longDescription', $detail);

        // List with featured filter
        $this->client->request('GET', '/api/projects?featured=true');
        self::assertResponseIsSuccessful();
        $list = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotEmpty($list);
        self::assertArrayNotHasKey('longDescription', $list[0]);

        // Update
        $this->authRequest('PUT', '/api/projects/'.$created['id'], json_encode([
            'title' => 'Updated Project',
        ]));
        self::assertResponseIsSuccessful();
        $updated = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals('Updated Project', $updated['title']);

        // Delete
        $this->authRequest('DELETE', '/api/projects/'.$created['id']);
        self::assertResponseStatusCodeSame(204);

        // Verify deleted
        $this->client->request('GET', '/api/projects/'.$created['id']);
        self::assertResponseStatusCodeSame(404);
    }

    public function testCreateProjectValidation(): void
    {
        $this->authenticate();

        $this->authRequest('POST', '/api/projects', json_encode([
            'title' => '',
            'category' => 'invalid',
        ]));
        self::assertResponseStatusCodeSame(422);
    }
}
