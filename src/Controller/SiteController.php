<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SiteController extends AbstractController
{

    /**
     * @Route("/tasks", name="tasks")
     */
    public function tasks(): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }
        return $this->render('site/tasks.html.twig');
    }

}