<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Donation;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DonationsController extends Controller
{
    /**
     * @Route("/donations", name="admin_donations")
     */
    public function indexAction(Request $request)
    {
        // Recupère la variable GET['year'] si elle existe
        $year = $request->query->get('year');
        if(empty($year)) {$year = 2019;}

        // Récupère le filtre $user
        if(null != $request->query->get('user'))
        {
            $user = $this->getDoctrine()->getRepository(User::class)
                ->find($request->query->get('user'));
        } else {

            $user = null;
        }

        // Récupère le filtre $paymentStatus
        if(empty($request->query->get('status')))
        {
            $paymentStatus = [Donation::PAY_IN_TRANSFER, Donation::PAY_PROCESSED];
        } else {

            $paymentStatus = [Donation::PAY_BASKET];
        }

        // Récupère les dons par YEAR()/$user/$paymentStatus = 4 avec la méthode 'findDonationsByYear'
        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->findDonationsByYear($year, $user, $paymentStatus);

        // Récupère le montant total des donations grâce à la méthode 'getDonationsTotalAmount'
        $totalAmount = $this->getDoctrine()->getRepository(Donation::class)
            ->getDonationsTotalAmount($donations);

        $allUser = $this->getDoctrine()->getRepository(User::class)
            ->findAll();

        return $this->render('admin/donations/admin_donations_index.html.twig', [
            'title' => 'Donations Admin',
            // Variables de Filtration
            'allUser' => $allUser,
            // Variables de Résultat
            'donations' => $donations,
            'paymentStatus' => Donation::PAYEMENT_STATUS,
            'totalAmount' => $totalAmount
        ]);
    }
}