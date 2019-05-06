<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends Controller
{
    /**
     * @Route("/users", name="admin_users")
     */
    public function indexAction(Request $request)
    {

        return $this->render('admin/users/index.html.twig', [
            'title' => 'Utilisateurs'
        ]);
    }
}