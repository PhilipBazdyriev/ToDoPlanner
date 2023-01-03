<?php

namespace ToDoPlanner\Task;

use App\Entity\Task;

class TaskStatus
{
    public const PENDING = 'pending';
    public const DONE = 'done';
    public const FAILED = 'failed';
}