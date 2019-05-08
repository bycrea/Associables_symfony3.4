<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {

        return $this->render('index.html.twig', [
            'title' => 'accueil'
        ]);
    }

    /**
     * @Route("/mentions", name="mentions")
     */
    public function mentionsAction(Request $request)
    {

        return $this->render('mentions.html.twig', [
            'title' => 'mentions'
        ]);
    }
}
