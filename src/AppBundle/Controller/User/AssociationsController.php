<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Donation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssociationsController extends Controller
{
    /**
     * @Route("/associations", name="user_associations")
     */
    public function UserAssociationsAction(Request $request)
    {
        // Récupère l'utilisateur
        $user = $this->getUser();

        // Récupère les dons en attente de transfère
        $userAssos = $this->getDoctrine()->getRepository(Assos::class)
            ->getByUserDonation($user);

        $userAssosAmount = [];
        foreach ($userAssos as $association) {
            $amount = $this->getDoctrine()->getRepository(Assos::class)
                ->getGivenAmount($association);
            $userAssosAmount[] = [$association, $amount];
        }


        return $this->render('user/associations.dashboard.html.twig', [
            'title' => 'Mon compte - Mes Assos',
            'userAssosAmount' => $userAssosAmount
        ]);
    }
}
