<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
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
        // Recupère le filtre des années $year
        $year = $request->query->get('year');
        if(empty($year)) {$year = 2019;}

        // Affichage de tous les utilisateurs
        $allUser = $this->getDoctrine()->getRepository(User::class)
            ->findAll();

        // Affichage de toutes les Associations
        $allAssos = $this->getDoctrine()->getRepository(Assos::class)
            ->findAll();

        // Récupère le filtre des associations $asso
        if(null != $request->query->get('asso'))
        {
            $asso = $this->getDoctrine()->getRepository(Assos::class)
                ->find($request->query->get('asso'));

        } else { $asso = null; }

        // Récupère le filtre des utilisateurs $user
        if(null != $request->query->get('user'))
        {
            $user = $this->getDoctrine()->getRepository(User::class)
                ->find($request->query->get('user'));

        } elseif($request->query->get('user') == '~') {

            $user = '~';

        } else { $user = null; }

        
        // Récupère le filtre des paiement $paymentStatus
        switch ($request->query->get('status'))
        {
            case null: $paymentStatus = [Donation::PAY_IN_TRANSFER, Donation::PAY_PROCESSED]; break;

            case 0: $paymentStatus = [Donation::PAY_BASKET]; break;

            case 3: $paymentStatus = [Donation::PAY_ERROR, Donation::PAY_REFUSED, Donation::PAY_CANCEL]; break;

            case 4: $paymentStatus = [Donation::PAY_IN_TRANSFER]; break;

            case 5: $paymentStatus = [Donation::PAY_PROCESSED]; break;

            default: $paymentStatus = [Donation::PAY_IN_TRANSFER, Donation::PAY_PROCESSED];
        }


        // Récupère les donations par $year/$user/$paymentStatus avec la méthode 'findDonationsByYear'
        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->adminDonationsFilter($year, $asso, $user, $paymentStatus);

        // Récupère le montant total de ces donations grâce à la méthode 'getDonationsTotalAmount'
        $totalAmount = $this->getDoctrine()->getRepository(Donation::class)
            ->getDonationsTotalAmount($donations);


        return $this->render('admin/donations/admin_donations_index.html.twig', [
            'title' => 'Donations Admin',

            // Variables de Filtration
            'allUser' => $allUser,
            'allAsso' => $allAssos,

            // Variables de Résultat
            'donations' => $donations,
            'paymentStatus' => Donation::PAYEMENT_STATUS,
            'totalAmount' => $totalAmount
        ]);
    }
}