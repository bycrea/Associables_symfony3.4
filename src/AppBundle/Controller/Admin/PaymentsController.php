<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use AppBundle\Entity\Payment;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentsController extends Controller
{
    /**
     * @Route("/payments", name="admin_payments")
     */
    public function indexAction(Request $request)
    {
        // La méthode 'findAwaitingPayments' récupère le total des dons en attentes de transfère trié par assos
        $awaitPayments = $this->getDoctrine()->getRepository(Assos::class)
            ->findAwaitingPayments();

        // Récupère les paiement déjà éffectués
        $oldPayments = $this->getDoctrine()->getRepository(Payment::class)
            ->findBy([],['createdAt' => 'DESC']);

        if (!empty($request->request->get('submit')))
        {
            // Instancie un nouveau Payment
            $payment = new Payment();

            // Récupère l'association concerné par le Payment
            $asso = $this->getDoctrine()->getRepository(Assos::class)
                ->find($request->request->get('id_asso'));

            // Set les propriétés du Payment
            $payment->setAmount($request->request->get('amount'));
            $payment->setPaymentStatus(Payment::PAY_PROCESSED);
            $payment->setPaymentMode($request->request->get('mode'));
            $payment->setAssociation($asso);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();

            // On persist et flush chaque donnations correspondantes au 'payment' avec son nouvel payment_id
            $donations = $this->getDoctrine()->getRepository(Donation::class)
                ->findBy(['assos' => $asso, 'paymentStatus' => Donation::PAY_IN_TRANSFER]);

            foreach ($donations as $donation)
            {
                $donation->setPayment($payment);
                $donation->setPaymentStatus(Donation::PAY_PROCESSED);

                $entityManager->persist($donation);
                $entityManager->flush();
            }

            $this->addFlash('success', 'Le paiement a été ajouté.');
            return $this->redirectToRoute('admin_payments');
        }


        return $this->render('admin/payments/admin_payments_index.html.twig', [
            'title' => 'Paiement Admin',
            'awaitPayments' => $awaitPayments,
            'oldPayments' => $oldPayments,
            'paymentMode' => Payment::PAYEMENT_MODE,
            'paymentStatus' => Payment::PAYEMENT_STATUS
        ]);
    }


    /**
     * @Route("/_ajax/payments", name="admin_ajax_payments_cancel")
     *
     * Méthode AJAX
     * Annule un 'payment' et remet le 'paymentStatus = PAY_IN_TRANSFER' des donations liées à celui-ci
     */
    public function cancelAction(Request $request)
    {
        // Récupère le 'payment' concerné grace à l'id injecté en AJAX
        $id = $request->request->get('id');
        $payment = $this->getDoctrine()->getRepository(Payment::class)->find($id);

        // Récupère les donations liées au 'payment'
        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->findBy(['payment' => $id]);

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($donations as $donation)
        {
            // Pour chaque donations, on retire le payment_id et on rétabli le 'paymentStatus'
            $donation->setPayment(null);
            $donation->setPaymentStatus(Donation::PAY_IN_TRANSFER);

            $entityManager->persist($donation);
            $entityManager->flush();
        }

        // Le 'paymentStatus' du 'payment' = PAY_CANCEL
        $payment->setPaymentStatus(Payment::PAY_CANCEL);
        $payment->setCreatedAt(new DateTime());
        $entityManager->persist($payment);
        $entityManager->flush();

        $this->addFlash('danger', 'Le paiement a bien été annulé.');
        return $this->json([
            'status' => true,
            'url' => $this->generateUrl('admin_payments')
        ]);
    }
}