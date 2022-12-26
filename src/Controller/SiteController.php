<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{

    /**
     * @Route("/tasks", name="tasks")
     */
    public function tasks(): Response
    {
        return $this->render('site/tasks.html.twig');
    }

}