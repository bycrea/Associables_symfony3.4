<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InfosController extends Controller
{
    /**
     * @Route("/infos", name="user_infos")
     */
    public function UserInfosAction(Request $request)
    {
        // Récupère l'utilisateur
        $user = $this->getUser();
        // Remplace la civilité en lettre en fonction du numéro
        $user->setGender(User::GENDERS[$user->getGender()]);

        return $this->render('user/infos.dashboard.html.twig', [
            'title' => 'Mon Compte - Mes Infos',
            'user' => $user
        ]);
    }
}
