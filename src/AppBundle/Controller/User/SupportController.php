<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends Controller
{
    /**
     * @Route("/support", name="user_support")
     */
    public function indexAction(Request $request)
    {

        return $this->render('user/support/index.html.twig', [
            'title' => 'SAV'
        ]);
    }
}
