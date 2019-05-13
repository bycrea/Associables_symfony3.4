<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Assos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AssociationsController extends Controller
{
    /**
     * @Route("/associations", name="user_associations")
     */
    public function UserAssociationsAction()
    {
        // Récupère l'utilisateur
        $user = $this->getUser();

        // On récupère d'abord les associations auquelles l'utilisateur à donné (paiement validé)
        $userAssos = $this->getDoctrine()->getRepository(Assos::class)
            ->getByUserDonation($user);

        // Si des associations existent :
        // On récupère ensuite les montants cumulés pour chaque association, pour cet utilisateur
        if(!empty($userAssos))
        {
            $userAssosAmount = [];
            foreach ($userAssos as $association)
            {
                $amount = $this->getDoctrine()->getRepository(Assos::class)
                    ->getGivenAmount($association, $user);

                // Renvoi un tableau [objet 'Assos', total des dons]
                $userAssosAmount[] = [$association, $amount];
            }
        } else {
            $userAssosAmount = [];
        }


        return $this->render('user/associations.dashboard.html.twig', [
            'title' => 'Mon compte - Mes Assos',
            'userAssosAmount' => $userAssosAmount
        ]);
    }
}
