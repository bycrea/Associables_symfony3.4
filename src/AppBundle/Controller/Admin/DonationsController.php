<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DonationsController extends Controller
{
    /**
     * @Route("/donations", name="admin_donations")
     */
    public function indexAction(Request $request)
    {

        return $this->render('admin/donations/index.html.twig', [
            'title' => 'Donations'
        ]);
    }
}