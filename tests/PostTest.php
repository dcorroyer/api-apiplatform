<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private const API_TOKEN = 'ab9fcc98a66535d7ced10664096c318af9b7887bc63226864a028f0bb0125a2e1761b247c85535adfa1a2f3899bfa3544d81c15fe149880f719199a1';

    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('admin@api.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $apiToken = new ApiToken();
        $apiToken->setToken(self::API_TOKEN);
        $apiToken->setUser($user);
        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCollection(): void
    {
        $response = $this->client->request('GET', '/api/posts', [
            'headers' => ['x-api-token' => self::API_TOKEN]
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            '@context'         => '/api/contexts/Post',
            '@id'              => '/api/posts',
            '@type'            => 'hydra:Collection',
            'hydra:totalItems' => 40,
            'hydra:view'       => [
                '@id'         => '/api/posts?page=1',
                '@type'       => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/posts?page=1',
                'hydra:last'  => '/api/posts?page=8',
                'hydra:next'  => '/api/posts?page=2',
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|DecodingExceptionInterface
     */
    public function testCreatePost(): void
    {
        $this->client->request('POST', '/api/posts', [
            'headers' => ['x-api-token' => self::API_TOKEN],
            'json'    => [
                'title'   => 'A title',
                'content' => 'A Test Post to test the text of the post.',
                'status'  => 'published',
                'author'  => '/api/authors/1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            'title'  => 'A title',
            'status' => 'published',
            'author' => '/api/authors/1'
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|DecodingExceptionInterface
     */
    public function testCreateInvalidPost(): void
    {
        $this->client->request('POST', '/api/posts', [
            'headers' => ['x-api-token' => self::API_TOKEN],
            'json'    => [
                'title'   => 'A title',
                'content' => 'A Test Post to test the text of the post.',
                'status'  => 'published'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            "@context"          => "/api/contexts/ConstraintViolationList",
            "@type"             => "ConstraintViolationList",
            "hydra:title"       => "An error occurred",
            "hydra:description" => "author: The author cannot be null",
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdatePost(): void
    {
        $this->client->request('PUT', '/api/posts/1', [
            'headers' => ['x-api-token' => self::API_TOKEN],
            'json'    => [
                'title' => 'An updated title',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id'   => '/api/posts/1',
            'title' => 'An updated title'
        ]);
    }
}
