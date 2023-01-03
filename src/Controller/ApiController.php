<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use ToDoPlanner\Task\Group;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/tasks", name="api_get_tasks", methods="GET")
     */
    public function getTasks(TaskRepository $taskRepo, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        if ($user === null)
        {
            throw new AccessDeniedException();
        }

        $groupLoader = new Group\Loader($user, $taskRepo);
        $userTaskGroupCollection = $groupLoader->loadDefaultCollection();
        return $this->json([
            'status' => 'ok',
            'group_collection' => $userTaskGroupCollection,
        ]);

    }

    /**
     * @Route("/api/tasks", name="api_create_task", methods="POST")
     */
    public function createTasks(Request $request, TaskRepository $taskRepo): Response
    {
        $user = $this->getUser();
        if ($user === null)
        {
            throw new AccessDeniedException();
        }

        $rawBody = $request->getContent();
        $parsedBody = json_decode($rawBody, true);
        return $this->json([
            'status' => 'ok',
            'task' => $parsedBody,
            //'group_list' => $group_list,
        ]);
    }

}
