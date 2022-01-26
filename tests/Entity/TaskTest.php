<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TaskTest extends KernelTestCase
{

    private $validator;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
        $this->task = new Task();
        $this->date = new \DateTime();
        $this->user = new User();
    }

    public function getTask(): Task
    {
        $this->task->setTitle('Test du titre');
        $this->task->setContent('Test du contenu');
        $this->task->setCreatedAt($this->date);
        $this->task->setUser($this->user);
        return $this->task;
    }

    public function assertHasError(Task $task, int $number = 0): void
    {
        $errors = $this->validator->validate($task);
        $messages = [];

        foreach ($errors as $error){
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testNotBlankTaskTitle(){
        $this->assertHasError($this->getTask(), 0);
    }

    public function testBlankTaskTitle(){
        $task = $this->getTask();
        $task->setTitle('');
        $this->assertHasError($task, 1);
    }

    public function testNotBlankTaskContent(){
        $this->assertHasError($this->getTask(), 0);
    }

    public function testBlankTaskContent(){
        $task = $this->getTask();
        $task->setContent('');
        $this->assertHasError($task, 1);
    }

    public function testTitle(): void
    {
        $title = 'Test du titre';
        $task = $this->getTask();
        $this->task->setTitle($title);
        $this->assertSame($title, $task->getTitle());
    }

    public function testCreateAt(): void
    {
        $task = $this->getTask();
        $this->assertSame($this->date, $task->getCreatedAt());
    }

    public function testContent(): void
    {
        $content = 'Test du contenu';
        $task = $this->getTask();
        $this->assertSame($content, $task->getContent());
    }

    public function testIsDone(): void
    {
        $task = $this->getTask();
        $task->toggle(true);
        $this->assertEquals(true, $task->getIsDone());
    }

    public function testIsNotDone(): void
    {
        $task = $this->getTask();
        $task->toggle(false);
        $this->assertEquals(false, $task->getIsDone());
    }

    public function testUser(): void
    {
        $task = $this->getTask();
        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertEquals($this->user->getId(), $task->getUser()->getId());
    }

}