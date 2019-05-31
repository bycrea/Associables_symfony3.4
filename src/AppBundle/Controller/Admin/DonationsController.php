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
     * @param $entity
     * @return \Doctrine\Common\Persistence\ObjectRepository
     *
     * Function getRepo() permet de simplifier le code pour accéder à un repository
     */
    public function getRepo($entity)
    {
        return $this->getDoctrine()->getRepository($entity);
    }


    /**
     * @Route("/donations", name="admin_donations")
     */
    public function indexAction(Request $request)
    {
        // Recupère le filtre des années $year
        $year = $request->query->get('year');
        if(empty($year)) {$year = 2019;}

        // Affichage de tous les utilisateurs
        $allUser = $this->getRepo(User::class)->findAll();

        // Affichage de toutes les Associations
        $allAssos = $this->getRepo(Assos::class)->findAll();

        // Récupère le filtre des associations $asso
        if(null != $request->query->get('asso'))
        {
            $asso = $this->getRepo(Assos::class)->find($request->query->get('asso'));

        } else { $asso = null; }

        // Récupère le filtre des utilisateurs $user
        if(null != $request->query->get('user'))
        {
            $user = $this->getRepo(User::class)->find($request->query->get('user'));

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


        // Récupère les donations par $year/$asso/$user/$paymentStatus avec la méthode 'findDonationsByYear'
        $donations = $this->getRepo(Donation::class)
            ->adminDonationsFilter($year, $asso, $user, $paymentStatus);

        // Récupère le montant total de ces donations grâce à la méthode 'getDonationsTotalAmount'
        $totalAmount = $this->getRepo(Donation::class)
            ->getDonationsTotalAmount($donations);


        return $this->render('admin/donations/admin_donations_index.html.twig', [
            'title' => 'Donations Admin',

            // Variables des Filtres
            'allUser' => $allUser,
            'allAsso' => $allAssos,

            // Variables des Résultats
            'donations' => $donations,
            'paymentStatus' => Donation::PAYEMENT_STATUS,
            'totalAmount' => $totalAmount
        ]);
    }
}
