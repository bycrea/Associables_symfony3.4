<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Category;
use AppBundle\Entity\Review;
use AppBundle\Form\CategoryType;
use AppBundle\Form\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends Controller
{
    /**
     * @Route("/reviews", name="user_reviews")
     */
    public function indexReviewsAction()
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)
            ->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('user/reviews.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Avis',
            'reviews' => $reviews
        ]);
    }


    /**
     * @Route("/edit/reviews/{id}", name="user_reviews_edit")
     */
    public function editReviewsAction(Request $request, $id)
    {
        $review = $this->getDoctrine()->getRepository(Review::class)
            ->find($id);

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis à bien été modifié');
            return $this->redirectToRoute('user_reviews');
        }

        return $this->render('user/reviews_edit.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Avis',
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/_ajax/delete/reviews", name="user_ajax_reviews_delete")
     */
    public function deleteReviewsAction()
    {

    }
}
