<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use ToDoPlanner\Task\Rest\CreateHandler;
use ToDoPlanner\Task\Rest\RetrieveHandler;
use ToDoPlanner\Task\Rest\UpdateHandler;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/tasks", name="api_get_tasks", methods="GET")
     */
    public function getTasks(EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        $taskRetriever = new RetrieveHandler($entityManager, $user);
        $userTaskGroupCollection = $taskRetriever->retrieveGroupCollection();
        return $this->json([
            'status' => 'ok',
            'group_collection' => $userTaskGroupCollection,
        ]);

    }

    /**
     * @Route("/api/tasks", name="api_create_task", methods="POST")
     */
    public function createTask(Request $request, TaskRepository $taskRepo, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        $jsonTaskData = $request->getContent();

        $taskCreator = new CreateHandler($entityManager, $user);
        try {
            $newTask = $taskCreator->createFromJson($jsonTaskData);
            return $this->json([
                'status' => 'ok',
                'task' => $newTask,
            ]);
        } catch (\Exception $exception) {
            return $this->json([
                'status' => 'bad',
                'error' => $exception->getMessage(),
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @Route("/api/tasks/{taskId}", name="api_update_task", methods="PUT")
     */
    public function updateTask(Request $request, TaskRepository $taskRepo, EntityManagerInterface $entityManager, $taskId): Response
    {

        $user = $this->getUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        $jsonTaskData = $request->getContent();

        $taskUpdater = new UpdateHandler($entityManager, $user);
        try {
            $updatedTask = $taskUpdater->updateFromJson($taskId, $jsonTaskData);
            return $this->json([
                'status' => 'ok',
                'task' => $updatedTask,
            ]);
        } catch (\Exception $exception) {
            return $this->json([
                'status' => 'bad',
                'error' => $exception->getMessage(),
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }

}
