<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use App\Tests\Traits\TaskTrait;
use App\Tests\Traits\UserTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoterTest extends TestCase
{

    private $voter;

    use UserTrait;
    use TaskTrait;
    use ProphecyTrait;


    protected function setUp(): void
    {
        parent::setUp();
        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $this->voter = new TaskVoter($security);
    }

    public function testVoteOnSometingElse()
    {
        $token = $this->prophesize(TokenInterface::class);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token->reveal(), null, ['FOOBAR']));
    }


    public function provideVoteTests()
    {
        $user = new User();
        $user->setEmail('aaaa@aaaa.com');
        $user->setPassword('aaaaaa');
        $user->setUsername('aaaaaa');

        $userNotOwnerOfTask = new User();
        $userNotOwnerOfTask->setEmail('bbbbbb@aaaa.com');
        $userNotOwnerOfTask->setPassword('bbbbbb');
        $userNotOwnerOfTask->setUsername('bbbbbbb');

        $task = new Task();
        $task->setTitle('toto');
        $task->setContent('coucou');
        $task->setUser($userNotOwnerOfTask);

        yield 'User not owner can not edit task' => [VoterInterface::ACCESS_DENIED, $user, $task];

        $task->setUser($userNotOwnerOfTask);

        $user->removeTask($task);
        yield 'User not owner can not delete task' => [VoterInterface::ACCESS_DENIED, $user, $task];

        $task = new Task();
        $task->setTitle('toto');
        $task->setContent('coucou');
        $task->setUser($user);
        $user->removeTask($task);
        yield 'User owner can delete task' => [VoterInterface::ACCESS_GRANTED, $user, $task];

    }

    /** @dataProvider provideVoteTests */
    public function testVote(int $expected, User $user, Task $task)
    {
        $token = new UsernamePasswordToken($user, 'password', 'provider_key', $user->getRoles());

        $this->assertSame($expected, $this->voter->vote($token, $task, ['task_edit', 'task_delete']));
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Task::class, $task);
    }

}