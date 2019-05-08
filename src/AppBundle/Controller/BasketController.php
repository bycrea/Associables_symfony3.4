<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use AppBundle\EventListener\LoginListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends Controller
{
    /**
     * @Route("/basket", name="basket")
     *
     * Affiche le panier
     * Récupère les dons via le formulaire avant le paiement
     */
    public function basketAction(Request $request)
    {

        $donations = null;
        $user = $this->getUser();
        $cookieId = $request->cookies->get('associables_basket');

        if(\is_object($user))
        {
            $donations = $this->getDoctrine()->getRepository(Donation::class)
                ->findBy(['user' => $user]);
        } elseif ($cookieId != null) {
            $donations = $this->getDoctrine()->getRepository(Donation::class)
                ->findBy(['cookieId' => $cookieId]);
        }

        $total_amount = 0;
        foreach ($donations as $donation) {
            $amount = $donation->getAmount();
            $total_amount += $amount;
        }

        return $this->render('basket.html.twig', [
            'title' => 'panier',
            'donations' => $donations,
            'total_amount' => $total_amount
        ]);
    }


    /**
     * @Route("/_ajax/add_to_basket", name="_ajax_add_to_basket")
     *
     * Create ou Update d'un don transmit en AJAX
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
                // OU avec la méthode doctrine grace à l'id_user récupéré plus haut
                // $user = $this->getDoctrine()->getRepository(User::class)->find($id_user);
                // OU avec la méthode symfony 'getUser'
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

        // Récupère le total des dons et leur somme dans le panier de l'utilisateur grace à la méthode
        // 'getBasketTotal' crée en amont dans 'DonationRepository' pour les transmettre en Front
        $basketTotal = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        // Retourne un objet JSON au paramètre SUCCESS de l'AJAX
        return $this->json([
            'status' => true,
            'quantity' => $basketTotal['quantity'],
            'amount' => $basketTotal['amount']
        ]);
    }


    /**
     * @Route("/_ajax/delete_from_basket", name="_ajax_delete_from_basket")
     *
     * Delete d'une donation transmise en AJAX
     */
    public function _ajaxDeleteFromBasketAction(Request $request)
    {
        // Recupère l'id de la donation envoyé en AJAX
        $id_don = $request->request->get('id_don');

        // Recupère l'id de l'association envoyé en AJAX
        $id_asso = $request->request->get('id_asso');

        // Initialise les variables transmisent aux méthodes 'DonationRepository'
        $id_user = null;
        $id_cookie = null;

        // Initialise l'EntityManager de Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On utlise la méthode 'getUser' de symfony pour savoir si un utilisateur est connecté
        if ($this->getUser())
        {
            // Si l'utilisateur est connecté on récupère son Id
            $id_user = $this->getUser()->getId();
        } else {
            // Sinon on utilise la valeur du coockie enregistré
            $id_cookie = $request->cookies->get('associables_basket');
        }

        // On verifie que le don existe grace à la méthode
        // 'existingBasketDonation' du 'DonationRepository'
        $donationExists = $this->getDoctrine()->getRepository(Donation::class)
            ->existingBasketDonation($id_asso, $id_user, $id_cookie);

        if ($donationExists && $donationExists->getId() == $id_don)
        {
            // Si le don existe on le supprime de la base de donnée
            $entityManager->remove($donationExists);
            $entityManager->flush();

        } else {

            // Si le don n'existe pas on retourne 'false'
            return $this->json(['status' => false]);
        }

        // Récupère le total des dons et leur somme dans le panier de l'utilisateur grace à la méthode
        // 'getBasketTotal' crée en amont dans 'DonationRepository' pour les transmettre en Front
        $basketTotal = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        // Retourne un objet JSON au paramètre SUCCESS de l'AJAX
        return $this->json([
            'status' => true,
            'quantity' => $basketTotal['quantity'],
            'amount' => $basketTotal['amount']
        ]);
    }
}
