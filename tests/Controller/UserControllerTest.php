<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Traits\RouteTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    use RouteTrait;

    private $client;


    public function setUp(): void
    {
        $this->client = static::createClient();

    }


    public function loginUserAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('Se connecter', [
            '_username' => 'admin@admin.com',
            '_password' => 'adminadmin',
        ]);

        $this->client->followRedirect();
    }

    public function loginUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('testuser@test.com');
        $this->client->loginUser($testUser);
    }

    public function testListActionAdmin()
    {
        $this->loginUserAdmin();
        $crawler = $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
    }

    public function testListActionNotAdmin()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateAction()
    {
        $this->loginUserAdmin();
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful('Response status 200');

        $this->assertEquals(1, $crawler->filter('label[for="user_username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_password_first"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_password_second"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_email"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_roles"]')->count());
        $this->assertEquals(1, $crawler->filter('select[name="user[roles]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[_token]"]')->count());

        $buttonCrawlerNode = $crawler->selectButton('Ajouter');

        $form = $buttonCrawlerNode->form();

        $form['user[username]'] = 'TestUsername';
        $form['user[password][first]'] = 'azertyazerty';
        $form['user[password][second]'] = 'azertyazerty';
        $form['user[email]'] = 'testusername@test.com';
        $form['user[roles]']->setValue('ROLE_USER');

        $this->client->submit($form);

        $this->assertResponseRedirects('/users', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testEditAction()
    {
        $this->loginUserAdmin();
        $crawler = $this->client->request('GET', '/users/' . $this->getFirstUser()->getId(). '/edit');
        $this->assertResponseIsSuccessful('Response status 200');

        $this->assertEquals(1, $crawler->filter('label[for="user_username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_password_first"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_password_second"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_email"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="user_roles"]')->count());
        $this->assertEquals(1, $crawler->filter('select[name="user[roles]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[_token]"]')->count());

        $buttonCrawlerNode = $crawler->selectButton('Modifier');

        $form = $buttonCrawlerNode->form();

        $form['user[username]'] = 'Nouveau';
        $form['user[password][first]'] = 'Nouveau';
        $form['user[password][second]'] = 'Nouveau';
        $form['user[email]'] = 'Nouveau@test.com';
        $form['user[roles]']->setValue('ROLE_ADMIN');

        $this->client->submit($form);

        $this->assertResponseRedirects('/users', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    private function getFirstUser()
    {
        $userRepository = static::$container->get(UserRepository::class);
        $users = $userRepository->findAll();
        return $users[0];
    }

}