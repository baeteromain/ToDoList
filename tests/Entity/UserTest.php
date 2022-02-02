<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Traits\TaskTrait;
use App\Tests\Traits\UserTrait;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{

    private $validator;

    use UserTrait;
    use TaskTrait;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
        $this->task = new Task();
        $this->date = new DateTime();
        $this->user = $this->getUser(new User());
    }

    public function assertHasError(User $user, int $number = 0): void
    {
        $errors = $this->validator->validate($user);
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testUsername()
    {
        $this->assertHasError($this->user, 0);
        $this->assertEquals('Test username', $this->user->getUsername());
        $user = $this->user->setUsername('');
        $this->assertHasError($user, 1);
    }

    public function testEmail()
    {
        $this->assertHasError($this->user, 0);
        $this->assertEquals('test@test.com', $this->user->getEmail());
        $user = $this->user->setEmail('');
        $this->assertHasError($user, 1);
        $user->setEmail('ddddddddd');
        $this->assertHasError($user, 1);
    }

    public function testRole()
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testPassword()
    {
        $this->assertEquals('test_password', $this->user->getPassword());
    }

    public function testTask()
    {
        $user = $this->user;
        $task = $this->getTask($this->task);
        $user->addTask($task);
        $this->assertTrue($user->getTask()->contains($task));
        $user->removeTask($task);
        $this->assertFalse($user->getTask()->contains($task));
    }

    public function testSalt()
    {
        $this->assertNull($this->user->getSalt());
    }

    public function testUserIdentifier()
    {
        $user = $this->user;
        $this->assertEquals('test@test.com', $user->getUserIdentifier());
    }

    public function testEraseCredential()
    {
        $user = $this->user;
        $this->assertNull($user->eraseCredentials());
    }
}