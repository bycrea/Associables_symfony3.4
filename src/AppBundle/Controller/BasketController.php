<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Exception;

class BasketController extends Controller
{
    /**
     * @Route("/basket", name="basket")
     *
     * Récupère et Affiche les donations d'un utilisateur ou d'une machine dans le panier
     * Affiche aussi le montant Total des dons en panier
     */
    public function basketAction(Request $request)
    {
        // Récupère l'utilisateur connecté et/ou le cookie 'associables_basket'
        $user = $this->getUser();
        $cookieId = $request->cookies->get('associables_basket');

        if (is_object($user))
        {
            // Si un utilisateur est connecté, on recherche les donations par $user
            $findBy = ['user' => $user, 'paymentStatus' => Donation::PAY_BASKET];
            // Récupère les donations en Panier
            $donations = $this->getDoctrine()->getRepository(Donation::class)
                ->findBy($findBy, ['createdAt' => 'DESC']);

        } else {

            // Sinon on recherche les donations avec l'id du cookie
            $findBy = ['cookieId' => $cookieId, 'paymentStatus' => Donation::PAY_BASKET];
            // Récupère les donations en Panier
            $donations = $this->getDoctrine()->getRepository(Donation::class)
                ->findBy($findBy, ['createdAt' => 'DESC']);
        }

        return $this->render('basket.html.twig', [
            'title' => 'Panier',
            'donations' => $donations
        ]);
    }


    /**
     * @Route("/_ajax/add_to_basket", name="_ajax_add_to_basket")
     *
     * AJAX: Create/Update une donation transmise en AJAX
     */
    public function _ajaxAddToBasketAction(Request $request)
    {
        // Recupère les variables $_POST envoyé en AJAX
        $id_asso = $request->request->get('id');
        $amount = $request->request->get('amount');

        // Initialise l'EntityManager de Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        try {
            // Un try/catch permettra de détecter d'éventuelles erreurs dans le déroulement du code
            // et ainsi de transmettre un message en Front (return false or true)
            if ($this->getUser())
            {
                // Si l'utilisateur est connecté on récupère son Id
                $id_user = $this->getUser()->getId();
                $id_cookie = null;

            } else {
                // Sinon on utilise la valeur du coockie enregistré
                $id_cookie = $request->cookies->get('associables_basket');
                $id_user = null;
            }

            // On verifie que le don n'existe pas déjà grace à la méthode 'existingBasketDonation'
            $donationExists = $this->getDoctrine()->getRepository(Donation::class)
                ->existingBasketDonation($id_asso, $id_user, $id_cookie);

            if ($donationExists)
            {
                // Si le don existe déjà, on enregistre le nouveau montant et on actualise le DateTime
                $donationExists->setAmount($amount)->setCreatedAt(new DateTime());
                $entityManager->persist($donationExists);

            } else {

                // Si le don n'existe pas, on instancie une nouvelle donation
                $newDonation = new Donation();

                // On récupère l'objet $association concerné
                $association = $this->getDoctrine()->getRepository(Assos::class)->find($id_asso);

                // On établi les propriétés de cette donation
                $newDonation->setAmount($amount)->setAssos($association);

                if ($user = $this->getUser()) {

                    // Si un utilisateur est connecté on transmet a notre donation
                    $newDonation->setUser($user);

                } else {

                    // Sinon on transmet l'id du cookie
                    $newDonation->setCookieId($id_cookie);
                }

                $entityManager->persist($newDonation);
            }

            // Enregistre toute les modification en base de donnée
            $entityManager->flush();

        } catch (Exception $e) {

            // Si une erreur est détecté on retourne 'false' en paramètre de Ajax::success
            return $this->json(['status' => false]);
        }

        // Récupère le nombre de dons et la somme total dans le panier
        $basketTotal = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        // Retourne un objet JSON en paramètre de Ajax::success
        return $this->json([
            'status' => true,
            'quantity' => $basketTotal['quantity'],
            'amount' => $basketTotal['amount']
        ]);
    }


    /**
     * @Route("/_ajax/delete_from_basket", name="_ajax_delete_from_basket")
     *
     * AJAX
     * Delete d'une donation transmise en AJAX
     */
    public function _ajaxDeleteFromBasketAction(Request $request)
    {
        // Recupère les variables $_POST envoyé en AJAX
        $id_don = $request->request->get('id_don');
        $id_asso = $request->request->get('id_asso');

        // Initialise les variables transmisent aux méthodes 'DonationRepository'
        $id_user = null;
        $id_cookie = null;

        // Initialise l'EntityManager de Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        if ($this->getUser())
        {
            // Si l'utilisateur est connecté on récupère son Id
            $id_user = $this->getUser()->getId();

        } else {

            // Sinon on utilise la valeur du coockie enregistré
            $id_cookie = $request->cookies->get('associables_basket');
        }

        // On verifie que le don existe et qu'il appartient bien à l'utilisateur connecté (ou au cookie)
        // grace à la méthode 'existingBasketDonation' du 'DonationRepository'
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

        // Récupère le nombre de dons et la somme total dans le panier
        $basketTotal = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        // Retourne un objet JSON en paramètre de Ajax::success
        return $this->json([
            'status' => true,
            'quantity' => $basketTotal['quantity'],
            'amount' => $basketTotal['amount']
        ]);
    }
}
