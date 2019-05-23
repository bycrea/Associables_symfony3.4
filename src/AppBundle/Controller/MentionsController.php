<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MentionsController extends Controller
{

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
