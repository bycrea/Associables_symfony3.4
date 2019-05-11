<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Donation;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DonationsController extends Controller
{
    /**
     * @Route("/donations", name="user_donations")
     */
    public function UserDonationsAction(Request $request)
    {
        // Récupère l'utilisateur
        $user = $this->getUser();

        // Récupère les dons en attente de transfère
        $awaitDons = $this->getDoctrine()->getRepository(Donation::class)
            ->findBy(['user' => $user,'paymentStatus' => Donation::PAY_IN_TRANSFER], ['createdAt' => 'DESC']);

        // Récupère les dons transféré
        $transDons = $this->getDoctrine()->getRepository(Donation::class)
            ->findBy(['user' => $user, 'paymentStatus' => Donation::PAY_PROCESSED], ['createdAt' => 'DESC']);


        return $this->render('user/donations.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Dons',
            'awaitDons' => $awaitDons,
            'transDons' => $transDons
        ]);
    }
}
