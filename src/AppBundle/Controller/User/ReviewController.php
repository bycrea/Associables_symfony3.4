<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends Controller
{
    /**
     * @Route("/review", name="user_review")
     */
    public function indexAction(Request $request)
    {

        return $this->render('user/reviews/index.html.twig', [
            'title' => 'SAV'
        ]);
    }
}
