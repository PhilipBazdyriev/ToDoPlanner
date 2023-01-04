<?php
namespace ToDoPlanner\Task\Rest;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ToDoPlanner\Task\TaskStatus;

class UpdateHandler
{

    private EntityManagerInterface $entityManager;
    private User $user;

    public function __construct(EntityManagerInterface $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }
    
    /**
     * @throws \Exception
     */
    public function updateFromJson(int $taskId, string $jsonTaskData): Task
    {
        $parsedTaskData = json_decode($jsonTaskData, true);
        if (!is_array($parsedTaskData))
        {
            throw new \Exception('JSON parse failed');
        }

        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        if ($task === null)
        {
            throw new \Exception('Undefined task: ' . $taskId);
        }
        if ($task->getUser()->getId() != $this->user->getId())
        {
            throw new \Exception('Denied');
        }
        
        if (isset($parsedTaskData['status'])) {
            $newStatus = $parsedTaskData['status'];
            if (($newStatus === TaskStatus::DONE || $newStatus === TaskStatus::FAILED) && $newStatus != $task->getStatus()) {
                $task->setStatus($newStatus);
            }
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }
}