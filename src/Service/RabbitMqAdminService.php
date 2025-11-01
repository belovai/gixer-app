<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RabbitMqAdminService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $managementUrl,
        private readonly string $managementUser,
        private readonly string $managementPassword,
        private readonly string $vhost,
    ) {
    }

    public function createUser(string $username, string $password): void
    {
        $this->request('PUT', sprintf('/api/users/%s', $username), [
            'json' => [
                'password' => $password,
                'tags' => 'probe',
            ],
        ]);

        $this->setUserPermissions($username);
    }

    public function deleteUser(string $username): void
    {
        $this->request('DELETE', sprintf('/api/users/%s', $username));
    }

    public function setUserPermissions(string $username): void
    {
        $this->request('PUT', sprintf('/api/permissions/%s/%s', $this->vhost, $username), [
            'json' => [
                'configure' => $username,
                'write' => $username,
                'read' => $username,
            ],
        ]);
    }

    public function createQueue(string $queueName): void
    {
        $this->request('PUT', sprintf('/api/queues/%s/%s', $this->vhost, $queueName), [
            'json' => [
                'auto_delete' => false,
                'durable' => true,
            ],
        ]);
    }

    public function deleteQueue(string $queueName): void
    {
        $this->request('DELETE', sprintf('/api/queues/%s/%s', $this->vhost, $queueName));
    }

    private function request(string $method, string $uri, array $options = []): void
    {
        $url = $this->managementUrl.$uri;

        $defaultOptions = [
            'auth_basic' => [$this->managementUser, $this->managementPassword],
        ];

        $response = $this->client->request($method, $url, array_merge($defaultOptions, $options));

        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('RabbitMQ API error: '.$response->getContent(false));
        }
    }
}
