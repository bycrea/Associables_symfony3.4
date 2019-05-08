<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends Controller
{
    /**
     * @Route("/payment", name="user_payment")
     */
    public function indexAction(Request $request)
    {

        return $this->render('user/reviews/index.html.twig', [
            'title' => 'Payment'
        ]);
    }
}
