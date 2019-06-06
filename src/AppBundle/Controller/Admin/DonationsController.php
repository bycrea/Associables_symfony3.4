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
     * Function getRepo() permet de simplifier le code pour accéder au repository d'une entité
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
        if(empty($year)) {$year = 2019;} //N.C. = 2019

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
            case 0:    $paymentStatus = [Donation::PAY_BASKET]; break;
            case 3:    $paymentStatus = [Donation::PAY_ERROR, Donation::PAY_REFUSED, Donation::PAY_CANCEL]; break;
            case 4:    $paymentStatus = [Donation::PAY_IN_TRANSFER]; break;
            case 5:    $paymentStatus = [Donation::PAY_PROCESSED]; break;
            default:   $paymentStatus = [Donation::PAY_IN_TRANSFER, Donation::PAY_PROCESSED];
        }

        // Récupère les donations triées avec la méthode 'adminDonationsFilter'
        $donations = $this->getRepo(Donation::class)
            ->adminDonationsFilter($year, $asso, $user, $paymentStatus);

        // Récupère le montant total de ces donations grâce à la méthode 'getDonationsTotalAmount'
        $totalAmount = $this->getRepo(Donation::class)
            ->getDonationsTotalAmount($donations);

        return $this->render('admin/donations/admin_donations_index.html.twig', [
            'title' => 'Donations Admin',

            // Affichage de tous les User & Assos (filtre)
            'allUser' => $this->getRepo(User::class)->findAll(),
            'allAsso' => $this->getRepo(Assos::class)->findAll(),

            // Variable de paymentStatus
            'paymentStatus' => Donation::PAYEMENT_STATUS,

            // Affichage des Résultats
            'donations' => $donations,
            'totalAmount' => $totalAmount
        ]);
    }
}
