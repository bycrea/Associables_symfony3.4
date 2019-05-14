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

        // Récupère les dons de l'utilisateur, par année et paymentStatus = 4 et 5 avec la méthode 'findDonationsByYear'
        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->findDonationsByYear($year, $user);

        // Récupère le montant total des donations grâce à la méthode 'getDonationsTotalAmount'
        $totalAmount = $this->getDoctrine()->getRepository(Donation::class)
            ->getDonationsTotalAmount($donations);


        return $this->render('user/donations.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Dons',
            'donations' => $donations,
            'paymentStatus' => Donation::PAYEMENT_STATUS,
            'totalAmount' => $totalAmount
        ]);
    }
}
