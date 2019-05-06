<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="user_dashboard")
     */
    public function indexAction(Request $request)
    {

        return $this->render('user/index.html.twig', [
            'title' => 'Dashboard User'
        ]);
    }
}
