<?php

namespace App\Tests\Controller;

use App\Tests\Traits\RouteTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use RouteTrait;

    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginWithWrongUser()
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('Se connecter', [
            '_username' => 'xxxx@xxxx.fr',
            '_password' => 'xxxxxx',
        ]);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert-danger')->count());
    }

    public function testLogoutUser()
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('Se connecter', [
            '_username' => 'admin@admin.com',
            '_password' => 'adminadmin',
        ]);

        $this->assertResponseRedirects($this->getRoute('homepage', $this->client));

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter(
            'html:contains("Bienvenue sur Todo List")')->count());

        $link = $crawler->selectLink('Se déconnecter')->link();
        $this->client->click($link);

        $crawler = $this->client->followRedirect();

        $this->assertResponseRedirects($this->getRoute('login', $this->client), 302, 'debug');

        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(1, $crawler->filter('input[name="_username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_password"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_csrf_token"]')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("You are logged in as")')->count());
    }

    public function testLoginCheck()
    {
        $crawler = $this->client->request('GET', '/login_check');

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

}