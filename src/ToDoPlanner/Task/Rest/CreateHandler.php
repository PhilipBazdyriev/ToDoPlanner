<?php
namespace ToDoPlanner\Task\Rest;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ToDoPlanner\Task\TaskStatus;

class CreateHandler
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
    public function createFromJson(string $jsonTaskData): Task
    {
        $parsedTaskData = json_decode($jsonTaskData, true);
        if (!is_array($parsedTaskData))
        {
            throw new \Exception('JSON parse failed');
        }

        $requiredFields = [
            'title',
            'description',
            'start_date',
            'end_date',
        ];

        $time_diff = strtotime($parsedTaskData['end_date']) - strtotime($parsedTaskData['start_date']);
        $duration_in_days = abs(round($time_diff / 86400)) + 1;

        foreach ($requiredFields as $requiredField)
        {
            if (!isset($parsedTaskData[$requiredField])) {
                throw new \Exception('Missing field "' . $requiredField . '"');
            }
        }

        $startDate = new \DateTime($parsedTaskData['start_date']);

        $newTask = new Task();
        $newTask->setTitle($parsedTaskData['title']);
        $newTask->setDescription($parsedTaskData['description']);
        $newTask->setStartDate($startDate);
        $newTask->setDurationInDays($duration_in_days);
        $newTask->setUser($this->user);
        $newTask->setStatus(TaskStatus::PENDING);

        $this->entityManager->persist($newTask);
        $this->entityManager->flush();

        return $newTask;
    }
}