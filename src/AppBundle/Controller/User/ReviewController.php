<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends Controller
{
    /**
     * @Route("/reviews", name="user_reviews")
     */
    public function UserReviewsAction(Request $request)
    {

        return $this->render('user/reviews.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Avis'
        ]);
    }
}
