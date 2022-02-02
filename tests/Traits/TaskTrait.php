<?php

namespace App\Tests\Traits;

use App\Entity\Task;
use App\Entity\User;
use DateTime;

trait TaskTrait{

    use UserTrait;

    protected function getTask($task): Task
    {
        $task->setTitle('Test du titre');
        $task->setContent('Test du contenu');
        $task->setCreatedAt(new DateTime());
        $task->setUser($this->getUser(new User()));
        return $task;
    }
}
