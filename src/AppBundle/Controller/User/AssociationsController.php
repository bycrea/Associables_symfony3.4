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

        // Récupère les associations auquelles l'utilisateur à donné
        $assoUserAndAmount = $this->getDoctrine()->getRepository(Assos::class)
            ->findUserAssosAndAmount($user);

        return $this->render('user/associations.dashboard.html.twig', [
            'title' => 'Mon compte - Mes Assos',
            'assoUserAndAmount' => $assoUserAndAmount
        ]);
    }
}
