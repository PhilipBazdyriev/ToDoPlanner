<?php

namespace ToDoPlanner\Task\Rest;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ToDoPlanner\Task\Group\Collection;
use ToDoPlanner\Task\Group\Loader;

class RetrieveHandler
{
    private EntityManagerInterface $entityManager;
    private User $user;

    public function __construct(EntityManagerInterface $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function retrieveGroupCollection(): Collection
    {
        $taskRepo = $this->entityManager->getRepository(Task::class);
        $groupLoader = new Loader($this->user, $taskRepo);
        return $groupLoader->loadDefaultCollection();
    }

}