<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Donation;
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

        // Recupère la variable GET['year'] si elle existe
        $year = $request->query->get('year');
        if(empty($year)) {$year = 2019;}

        // Récupère les dons par YEAR()/$user/$paymentStatus = 4 avec la méthode 'findDonationsByYear'
        $awaitDons = $this->getDoctrine()->getRepository(Donation::class)
            ->findDonationsByYear($year, $user, [Donation::PAY_IN_TRANSFER]);

        // Récupère les dons par YEAR()/$user/$paymentStatus = 5 avec la méthode 'findDonationsByYear'
        $transDons = $this->getDoctrine()->getRepository(Donation::class)
            ->findDonationsByYear($year, $user, [Donation::PAY_PROCESSED]);

        // Récupère le montant total des donations grâce à la méthode 'getDonationsTotalAmount'
        $totalAmount = $this->getDoctrine()->getRepository(Donation::class)
            ->getDonationsTotalAmount(array_merge($awaitDons,$transDons));


        return $this->render('user/donations.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Dons',
            'awaitDons' => $awaitDons,
            'transDons' => $transDons,
            'totalAmount' => $totalAmount
        ]);
    }
}
