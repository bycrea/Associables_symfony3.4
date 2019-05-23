<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use AppBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        // Récupère les associations récement solicitées
        $associations = $this->getDoctrine()->getRepository(Assos::class)
            ->findMostRecent(3);

        // Récupère les avis utilisateur récents ou la note n'est pas en dessous de $minMark
        $reviews = $this->getDoctrine()->getRepository(Review::class)
            ->findHomePageReviews(3, 2);

        return $this->render('index.html.twig', [
            'title' => 'accueil',
            'associations' => $associations,
            'reviews' => $reviews
        ]);
    }


    /**
     * 'user_redirect' permet de rediriger l'utilsateur en fonction de son ROLE
     * de son panier et du referer (voir security.yml => default_target_path: /users/redirect)
     *
     * @Route("/users/redirect", name="user_redirect")
     */
    public function usersRedirectAction()
    {
        // Récupère l'utilisateur s'il est connecté
        if($user = $this->getUser())
        {
            // Si le 'ROLE_ADMIN' existe pour cet Utilisateur
            if(false !== array_search('ROLE_ADMIN', $user->getRoles(), true))
            {
                return $this->redirectToRoute('admin_donations');

            } else {

                // Sinon on verifi si l'utilisateur a des dons en attente
                $donInBasket = $this->getDoctrine()->getRepository(Donation::class)
                    ->findBy(['user' => $user, 'paymentStatus' => Donation::PAY_BASKET]);

                // Si oui on le redirige vers le Panier
                if(!empty($donInBasket))
                {
                    return $this->redirectToRoute('basket');
                } else {
                    // Sinon on redirige vers le Dashboard
                    return $this->redirectToRoute('user_donations');
                }
            }
        }

        // Sinon on redirige home
        return $this->redirectToRoute('home');
    }
}
