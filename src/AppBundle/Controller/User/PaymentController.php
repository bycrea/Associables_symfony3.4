<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Donation;
use AppBundle\Entity\Transaction;
use Beelab\PaypalBundle\Paypal\Exception;
use Beelab\PaypalBundle\Paypal\Service;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends Controller
{

    /**
     * @Route("/payment", name="user_payment")
     *
     * Récupère les donations du panier liées à un utilisateur
     * Envoi les donations dans une transaction PayPal grâce à 'BeelabPaypalBundle'
     */
    public function paymentAction(Service $service)
    {
        // Récupère l'utilisateur
        $user = $this->getUser();

        // Récupère les donations liées à l'utilisateur
        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->findBy(['user' => $user]);

        // Récupère le montant total
        $totalAmount = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($user->getId())['amount'];

        // Verifie que le montant soit supérieur à zéro avant de lancer une transaction
        if ($totalAmount != null && $totalAmount > 0)
        {
            // Création d'une nouvelle entity Transaction
            $transaction = new Transaction($totalAmount);

        } else {

            // Redirection vers la panier
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('basket');
        }

        // 'setDonations' est une méthode crée dans l'entity Transaction pour
        // transmettre les détails de la transaction à PayPal via le Bundle
        $transaction->setDonations($donations);

        try {
            // Méthodes liées au Service pour lancer la transaction et Updater la table Transaction
            $response = $service->setTransaction($transaction)->start();
            $this->getDoctrine()->getManager()->persist($transaction);
            $this->getDoctrine()->getManager()->flush();

            // Redirige vers l'url de PayPal généré par le Service
            return $this->redirect($response->getRedirectUrl());
        } catch (Exception $e) {
            throw new HttpException(503, 'Erreur de paiement', $e);
        }
    }


    /**
     * @Route("/payment_cancel", name="user_payment_cancel")
     *
     * Vérifie la validité du token retourné par PayPal
     * (correspondance avec le token de la transaction envoyé)
     * Update la table Transaction et Redirige vers le panier
     */
    public function canceledPaymentAction(Request $request)
    {
        $token = $request->query->get('token');
        $transaction = $this->getDoctrine()->getRepository(Transaction::class)->findOneByToken($token);
        if (null === $transaction) {
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));
        }
        $transaction->cancel(null);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('basket');
    }


    /**
     * @Route("/payment_success", name="user_payment_success")
     *
     * Modifie le 'paymentStatus' des donations en cas de success ou d'erreur
     * Redirige vers le dashboard utilisateur
     */
    public function completedPaymentAction(Service $service, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $token = $request->query->get('token');
        $transaction = $this->getDoctrine()->getRepository(Transaction::class)->findOneByToken($token);

        if (null === $transaction) {
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));
        }

        // Récupère l'utilisateur
        $user = $this->getUser();

        // Récupère les donations liées à l'utilisateur
        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->findBy(['user' => $user]);

        // transmettre les détails de la transaction pour getItems
        $transaction->setDonations($donations);

        $service->setTransaction($transaction)->complete();
        $entityManager->flush();

        if (!$transaction->isOk())
        {
            $status = Donation::PAY_ERROR;
            $this->addFlash('danger', 'Une erreur est survenue, veuillez contacter le service PayPal.');

        } else {

            $status = Donation::PAY_IN_TRANSFER;
            $this->addFlash('success', 'Paiement validé, merci pour vos dons et votre confiance.');
        }

        foreach($donations as $donation)
        {
            $donation->setPaymentStatus($status);
            $donation->setPaymentMode(Donation::PAY_PAYPAL);
            $entityManager->persist($donation);
        }
        $entityManager->flush();

        return $this->redirectToRoute('user_dashboard');
    }
}
