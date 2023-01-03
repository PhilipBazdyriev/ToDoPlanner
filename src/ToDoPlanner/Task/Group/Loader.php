<?php

namespace ToDoPlanner\Task\Group;

use App\Entity\User;
use App\Repository\TaskRepository;
use Exception;

class Loader
{
    private User $user;
    private TaskRepository $taskRepo;

    public function __construct(User $user, TaskRepository $taskRepo)
    {
        $this->user = $user;
        $this->taskRepo = $taskRepo;
    }

    /**
     * Loads default user's task group collection
     * @return \ToDoPlanner\Task\Group\Collection
     */
    public function loadDefaultCollection(): Collection
    {
        $groupCollection = new Collection();

        $groupCollection->addGroup($this->loadGroup(Type::TODAY));
        $groupCollection->addGroup($this->loadGroup(Type::THIS_WEEK));
        $groupCollection->addGroup($this->loadGroup(Type::THIS_MONTH));

        return $groupCollection;
    }

    /**
     * @throws Exception
     */
    public function loadGroup(string $groupType): Group
    {

        $group = new Group($groupType, $groupType); // TODO do something with group title $translator->trans('group.type.this_month')

        $tasks = $this->taskRepo->findCurrentByGorupType($this->user->getId(), $groupType);
        $group->addTasks($tasks);

        return $group;
    }

}