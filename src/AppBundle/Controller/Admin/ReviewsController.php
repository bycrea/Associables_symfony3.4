<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends Controller
{
    /**
     * @Route("/reviews", name="admin_reviews")
     */
    public function indexAction(Request $request)
    {

        return $this->render('admin/reviews/index.html.twig', [
            'title' => 'Avis'
        ]);
    }
}