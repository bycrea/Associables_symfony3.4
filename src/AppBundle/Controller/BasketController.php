<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use AppBundle\Entity\User;
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
     */
    public function _ajaxAddToBasketAction(Request $request)
    {
        $id_asso = $request->request->get('id');
        $amount = $request->request->get('amount');
        $id_user = null;
        $id_cookie = null;

        $entityManager = $this->getDoctrine()->getManager();

        try {

            if ($this->getUser()) {
                $id_user = $this->getUser()->getId();
            } else {
                $id_cookie = $request->cookies->get('associables_basket');
            }

            $donationExists = $this->getDoctrine()->getRepository(Donation::class)
                ->existingBasketDonation($id_asso, $id_user, $id_cookie);

            if ($donationExists)
            {

                $donationExists->setAmount($amount);
                $donationExists->setCreatedAt(new \DateTime());
                $entityManager->persist($donationExists);

            } else {
                $association = $this->getDoctrine()->getRepository(Assos::class)->find($id_asso);
                // $user = $this->getDoctrine()->getRepository(User::class)->find($id_user);
                // OU
                $user = $this->getUser();

                $newDonation = new Donation();
                $newDonation->setAmount($amount)->setAssos($association);

                if ($this->getUser()) {
                    $newDonation->setUser($user);
                } else {
                    $newDonation->setCookieId($id_cookie);
                }

                $entityManager->persist($newDonation);
            }

            $entityManager->flush();

        } catch (\Exception $e) {

            return $this->json(['status' => false]);

        }

        $donationTotal = $this->getDoctrine()->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        return $this->json(['status' => true, 'total' => $donationTotal]);
    }
}
