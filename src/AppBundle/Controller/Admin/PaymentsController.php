<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentsController extends Controller
{
    /**
     * @Route("/payments", name="admin_payments")
     */
    public function indexAction(Request $request)
    {

        return $this->render('admin/payments/index.html.twig', [
            'title' => 'Paiement'
        ]);
    }
}