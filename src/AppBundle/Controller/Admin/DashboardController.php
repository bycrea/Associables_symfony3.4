<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function indexAction(Request $request)
    {

        return $this->render('dashboard.html.twig', [
            'title' => 'Dashboard Admin'
        ]);
    }
}