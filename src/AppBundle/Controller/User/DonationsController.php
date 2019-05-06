<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DonationsController extends Controller
{
    /**
     * @Route("/donations", name="user_donations")
     */
    public function indexAction(Request $request)
    {

        return $this->render('user/donations/index.html.twig', [
            'title' => 'Donations'
        ]);
    }
}
