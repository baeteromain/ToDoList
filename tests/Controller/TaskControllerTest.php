<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Traits\RouteTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use RouteTrait;

    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testListTask()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects($this->getRoute('login', $this->client), '302');

        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('input[name="_username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_password"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_csrf_token"]')->count());

        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('Se connecter', [
            '_username' => 'admin@admin.com',
            '_password' => 'adminadmin',
        ]);
        $this->assertSelectorNotExists('.alert.alert-danger');

        $this->assertResponseRedirects($this->getRoute('task_list', $this->client), '302');

        $crawler = $this->client->followRedirect();
        $this->assertSame('Marquer comme faite', $crawler->filter('button.btn.btn-success')->text());

    }

    public function testListActionDone()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects($this->getRoute('login', $this->client), '302');
        $this->client->followRedirect();
        $this->loginWithAdmin();

        $this->client->request('GET', '/tasks_done');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateAction()
    {
        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful('Response status 200');

        $this->assertEquals(1, $crawler->filter('label[for="task_title"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="task_content"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="task[_token]"]')->count());

        $crawler = $this->client->submitForm('Ajouter', [
            'task[title]' => 'Test du super titre',
            'task[content]' => 'Test de la super description',
        ]);

        $this->assertResponseRedirects('/tasks', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function testEditAction()
    {
        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/' . $this->getTask()->getId() . '/edit');
        $this->assertResponseIsSuccessful('Response status 200');

        $this->assertEquals(1, $crawler->filter('label[for="task_title"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertEquals(1, $crawler->filter('label[for="task_content"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="task[_token]"]')->count());

        $crawler = $this->client->submitForm('Modifier', [
            'task[title]' => 'Test du super titre',
            'task[content]' => 'Test de la super description',
        ]);

        $this->assertResponseRedirects('/tasks', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function testToggleTaskAction()
    {
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/tasks/' . $this->getTask(true)->getId() . '/toggle');
        $this->assertResponseRedirects('/tasks', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testDeleteTaskAction()
    {
        // Delete with Admin acount
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/tasks/' . $this->getTask()->getId() . '/delete');
        $this->assertResponseRedirects('/tasks', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        // Logout admin
        $link = $crawler->selectLink('Se déconnecter')->link();
        $this->client->click($link);
        $crawler = $this->client->followRedirect();

        $this->assertResponseRedirects($this->getRoute('login', $this->client), 302, 'debug');

        // Get user of the task
        $user = $this->getUserOfTask($this->getTask());

        //Login with this
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUserOwner = $userRepository->findOneBy(['id' => $user->getId()]);
        $this->client->loginUser($testUserOwner);

        //Delete task with this user (owner)
        $crawler = $this->client->request('GET', '/tasks/' . $this->getTask()->getId() . '/delete');
        $this->assertResponseRedirects('/tasks', '302');
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful('Response status 200');
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        // Logout userOwner
        $link = $crawler->selectLink('Se déconnecter')->link();
        $this->client->click($link);
        $crawler = $this->client->followRedirect();

        $this->assertResponseRedirects($this->getRoute('login', $this->client), 302, 'debug');

        // Login with UserNotOwner
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUserNotOwner = $userRepository->findOneByEmail('testuser@test.com');
        $this->client->loginUser($testUserNotOwner);

        $crawler = $this->client->request('GET', '/tasks/' . $this->getTask()->getId() . '/delete');
        $this->assertResponseStatusCodeSame(403);

    }

    private function loginWithAdmin()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@admin.com');
        return $this->client->loginUser($testUser);
    }

    private function getTask($isNotDone = false)
    {
        $taskController = static::getContainer()->get(TaskRepository::class);
        if ($isNotDone) {
            $task = $taskController->findOneBy(['isDone' => 0]);
            return $task;
        }

        $tasks = $taskController->findAll();
        return $tasks[0];
    }

    private function getUserOfTask(Task $task)
    {
        return $task->getUser();
    }


}