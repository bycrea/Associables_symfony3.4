<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Donation;
use AppBundle\Entity\Transaction;
use Beelab\PaypalBundle\Paypal\Exception;
use Beelab\PaypalBundle\Paypal\Service;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends Controller
{

    /**
     * @Route("/payment", name="user_payment")
     */
    public function paymentAction(Request $request, Service $service)
    {
        $user = $this->getUser();

        $donations = $this->getDoctrine()->getRepository(Donation::class)
            ->findBy(['user' => $user]);

        $total_amount = 0;
        foreach ($donations as $donation) {
            $amount = $donation->getAmount();
            $total_amount += $amount;
        }

        $amount = $total_amount;
        $transaction = new Transaction($amount);
        $transaction->setDonations($donations);

        try {
            $response = $service->setTransaction($transaction)->start();
            $this->getDoctrine()->getManager()->persist($transaction);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($response->getRedirectUrl());
        } catch (Exception $e) {
            throw new HttpException(503, 'Payment error', $e);
        }
    }

    /**
     * @Route("/payment_cancel", name="user_payment_cancel")
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

        return $this->render('user/infos/index.html.twig', [
            'title' => 'cancel'
        ]);
    }

    /**
     * @Route("/payment_success", name="user_payment_success")
     */
    public function completedPaymentAction(Service $service, Request $request)
    {
        $token = $request->query->get('token');
        $transaction = $this->getDoctrine()->getRepository(Transaction::class)->findOneByToken($token);
        if (null === $transaction) {
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));
        }
        $service->setTransaction($transaction)->complete();
        $this->getDoctrine()->getManager()->flush();
        if (!$transaction->isOk())
        {
            return $this->render('user/infos/index.html.twig', [
                'title' => 'cancel'
            ]);
        }

        return $this->render('user/infos/index.html.twig', [
            'title' => 'success'
        ]);
    }
}
