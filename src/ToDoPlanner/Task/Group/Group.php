<?php

namespace ToDoPlanner\Task\Group;

use App\Entity\Task;

class Group implements \JsonSerializable
{

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var array
     */
    private array $tasks = [];

    public function __construct(string $type, string $title)
    {
        $this->type = $type;
        $this->title = $title;
    }

    public function jsonSerialize(): array
    {
        $tasks_serialized = [];
        foreach ($this->tasks as $task)
        {
            $tasks_serialized[] = $task->toPublicJson();
        }
        return [
            'type' => $this->type,
            'title' => $this->title,
            'tasks' => $tasks_serialized,
        ];
    }

    public function addTask(Task $task): void
    {
        if (!in_array($task, $this->tasks))
        {
            $this->tasks[] = $task;
        }
    }

    public function addTasks(array $tasks): void
    {
        foreach ($tasks as $task)
        {
            $this->addTask($task);
        }
    }
}