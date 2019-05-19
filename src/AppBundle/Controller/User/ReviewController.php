<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Review;
use AppBundle\Form\ReviewType;
use DateTime;
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

            $review->setCreatedAt(new DateTime());
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis à bien été modifié.');
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
    public function deleteReviewsAction(Request $request)
    {
        $id = $request->request->get('id');
        $review = $this->getDoctrine()->getRepository(Review::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($review);
        $entityManager->flush();

        $this->addFlash('success', 'Votre avis a bien été supprimé.');
        return $this->json([
            'status' => true,
            'url' => $this->generateUrl('user_reviews')
        ]);
    }
}
