<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends Controller
{
    /**
     * @Route("/reviews", name="admin_reviews")
     */
    public function indexReviewsAction()
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/reviews/admin_reviews_index.html.twig', [
            'title' => 'Avis Admin',
            'reviews' => $reviews
        ]);
    }


    /**
     * @Route("/_ajax/delete/reviews", name="admin_ajax_reviews_delete")
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
            'url' => $this->generateUrl('admin_reviews')
        ]);
    }
}