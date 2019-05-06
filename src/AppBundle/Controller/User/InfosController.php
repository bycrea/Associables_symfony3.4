<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InfosController extends Controller
{
    /**
     * @Route("/infos", name="user_infos")
     */
    public function indexAction(Request $request)
    {

        return $this->render('user/infos/index.html.twig', [
            'title' => 'Infos'
        ]);
    }
}
