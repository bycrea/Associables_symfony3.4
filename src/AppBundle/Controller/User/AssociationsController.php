<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssociationsController extends Controller
{
    /**
     * @Route("/mes_associations", name="user_associations")
     */
    public function associationsAction(Request $request)
    {
        // Associations aux quelles l'utilisateur à déjà donné
    }
}
