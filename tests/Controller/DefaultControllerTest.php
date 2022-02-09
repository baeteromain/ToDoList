<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexActionUserDisconnected()
    {
        $this->client->request('GET', '/');

        $this->client->followRedirects();

        $this->assertResponseRedirects();

        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('input[name="_username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_password"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_csrf_token"]')->count());
    }

    public function testIndexActionUserConnected()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@admin.com');
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

}