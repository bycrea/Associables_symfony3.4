<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends Controller
{
    /**
     * @Route("/basket", name="basket")
     */
    public function basketAction(Request $request)
    {

        return $this->render('basket.html.twig', [
            'title' => 'panier'
        ]);
    }

    /**
     * @Route("/_ajax/add_to_basket", name="_ajax_add_to_basket")
     *
     * Enregistrement en base de donnée d'un don transmit en AJAX
     */
    public function _ajaxAddToBasketAction(Request $request)
    {
        // Recupère les variables $_POST envoyé en AJAX
        $id_asso = $request->request->get('id');
        $amount = $request->request->get('amount');

        // Initialise les variables transmisent aux méthodes 'DonationRepository'
        $id_user = null;
        $id_cookie = null;

        // Initialise l'EntityManager de Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // Un try/catch permettra de détecter d'éventuelles erreurs dans le déroulement du code
        // et ainsi de transmettre un message en Front (return false or true)
        try {

            // On utlise la méthode 'getUser' de symfony pour savoir si un utilisateur est connecté
            if ($this->getUser())
            {
                // Si l'utilisateur est connecté on récupère son Id
                $id_user = $this->getUser()->getId();
            } else {
                // Sinon on utilise la valeur du coockie enregistré
                $id_cookie = $request->cookies->get('associables_basket');
            }

            // On verifie que le don n'existe pas déjà grace à la méthode
            // 'existingBasketDonation' du 'DonationRepository'
            // '$donationExists' enregistre le résultat de la requête
            $donationExists = $this->getDoctrine()->getRepository(Donation::class)
                ->existingBasketDonation($id_asso, $id_user, $id_cookie);

            if ($donationExists)
            {
                // Si le don existe déjà, on enregistre le nouveau montant (celui-ci peut être le même)
                $donationExists->setAmount($amount);
                $donationExists->setCreatedAt(new \DateTime());
                $entityManager->persist($donationExists);

            } else {

                // Si le don n'existe pas, on enregistre celui-ci
                // On récupère l'objet $association
                $association = $this->getDoctrine()->getRepository(Assos::class)->find($id_asso);

                // On récupère l'objet $user
                // OU la méthode doctrine grace à l'id_user récupéré plus haut
                // $user = $this->getDoctrine()->getRepository(User::class)->find($id_user);
                // OU la méthode symfony 'getUser'
                $user = $this->getUser();

                // On crée une nouvelle donation
                $newDonation = new Donation();

                // On établi les paramètres de cette donation
                $newDonation
                    ->setAmount($amount)
                    ->setAssos($association);
                if ($this->getUser()) {
                    $newDonation->setUser($user);
                } else {
                    $newDonation->setCookieId($id_cookie);
                }

                $entityManager->persist($newDonation);
            }

            // Enregistre toute les modification en base de donnée
            $entityManager->flush();

        } catch (\Exception $e) {

            // Si une erreur est détecté on retourne 'false' au paramètre SUCCESS de l'AJAX
            return $this->json(['status' => false]);
        }

        // Récupère le total des dons dans le panier de l'utilisateur grace à la méthode
        // 'getBasketTotal' crée en amont dans 'DonationRepository' pour les transmettre en Front
        $donationTotal = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        // Retourne un objet JSON au paramètre SUCCESS de l'AJAX
        return $this->json(['status' => true, 'total' => $donationTotal]);
    }
}
