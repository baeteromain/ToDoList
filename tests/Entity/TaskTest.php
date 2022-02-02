<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Traits\TaskTrait;
use DateTime;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TaskTest extends KernelTestCase
{

    private $validator;

    use TaskTrait;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
        $this->task = $this->getTask(new Task());
        $this->date = new DateTime();
        $this->user = new User();
    }

    public function assertHasError(Task $task, int $number = 0): void
    {
        $errors = $this->validator->validate($task);
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testNotBlankTaskTitle()
    {
        $this->assertHasError($this->task, 0);
    }

    public function testBlankTaskTitle()
    {
        $task = $this->task;
        $task->setTitle('');
        $this->assertHasError($task, 1);
    }

    public function testNotBlankTaskContent()
    {
        $this->assertHasError($this->task, 0);
    }

    public function testBlankTaskContent()
    {
        $task = $this->task;
        $task->setContent('');
        $this->assertHasError($task, 1);
    }

    public function testTitle(): void
    {
        $title = 'Test du titre';
        $task = $this->task;
        $this->task->setTitle($title);
        $this->assertSame($title, $task->getTitle());
    }

    /**
     * @throws ReflectionException
     */
    public function testId(): void
    {
        $task = $this->task;
        $this->set($task, 1);
        $this->assertSame(1, $task->getId());

    }


    public function testCreateAt(): void
    {
        $task = $this->task;
        $task->setCreatedAt($this->date);
        $this->assertSame($this->date, $task->getCreatedAt());
    }

    public function testContent(): void
    {
        $content = 'Test du contenu';
        $task = $this->task;
        $this->assertSame($content, $task->getContent());
    }

    public function testIsPassToDone(): void
    {
        $task = $this->task;
        $task->setIsDone(false);
        $task->toggle(true);
        $this->assertEquals(true, $task->getIsDone());
        $this->assertEquals(true, $task->isDone());
    }

    public function testIsPassToNotDone(): void
    {
        $task = $this->task;
        $task->setIsDone(true);
        $task->toggle(false);
        $this->assertEquals(false, $task->getIsDone());
    }

    public function testUser(): void
    {
        $task = $this->task;
        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertEquals($this->user->getId(), $task->getUser()->getId());
    }

    /**
     * @throws ReflectionException
     */
    public function set($entity, $value, $propertyName = 'id')
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($entity, $value);
    }

}