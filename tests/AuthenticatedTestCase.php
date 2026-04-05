<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AuthenticatedTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected ?string $jwtToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->purgeDatabase();
    }

    protected function createTestAdmin(): void
    {
        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('test@admin.dev');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, 'password'));

        $em->persist($user);
        $em->flush();
    }

    protected function authenticate(): void
    {
        $this->createTestAdmin();

        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'test@admin.dev', 'password' => 'password']));

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->jwtToken = $data['token'];
    }

    protected function authRequest(string $method, string $uri, ?string $body = null): void
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$this->jwtToken,
        ];

        $this->client->request($method, $uri, [], [], $headers, $body);
    }

    private function purgeDatabase(): void
    {
        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $connection = $em->getConnection();

        $tables = $connection->createSchemaManager()->listTableNames();
        $connection->executeStatement('SET session_replication_role = replica');
        foreach ($tables as $table) {
            $name = trim($table, '"');
            if ('doctrine_migration_versions' === $name) {
                continue;
            }
            $connection->executeStatement(sprintf('TRUNCATE TABLE %s CASCADE', $connection->quoteIdentifier($name)));
        }
        $connection->executeStatement('SET session_replication_role = DEFAULT');
    }
}
