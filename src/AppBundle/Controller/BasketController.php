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
     */
    public function _ajaxAddToBasketAction(Request $request)
    {
        $id_assciation = $request->request->get('id');
        $amount_donation = $request->request->get('amount');

        $donation = $this->getDoctrine()->getRepository(Donation::class)
            ->find($id);




        echo $id_assciation;
        echo $amount_donation;
        exit;
    }
}
