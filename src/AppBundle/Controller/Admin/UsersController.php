<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Donation;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends Controller
{
    /**
     * @Route("/users/{filter}", name="admin_users")
     */
    public function indexAction($filter = null)
    {

        // Si aucun filtre n'est entré en paramètre
        if(!$filter)
        {
            // OrderBy par défaut
            $orderBy = ['user.id', 'ASC'];

        } else {

            // Sinon on détermine OrderBy en fonction du filtre
            switch ($filter) {

                case 'id' : $orderBy = ['user.id', 'ASC']; break;

                case 'username' : $orderBy = ['user.username', 'ASC']; break;

                case 'roles' : $orderBy = ['user.roles', 'DESC']; break;

                case 'created' : $orderBy = ['user.createdAt', 'DESC']; break;

                case 'lastLog' : $orderBy = ['user.lastLogin', 'DESC']; break;

                case 'nb' : $orderBy = ['nb', 'DESC']; break;

                case 'amount' : $orderBy = ['amount', 'DESC']; break;

                // Defaut évite les entrées aléatoire dans l'url
                default: $orderBy = ['user.id', 'ASC']; break;
            }
        }

        // Récupère les utilisateurs en fonction du filtre grâce à la méthode 'OrderByCustom'
        $users = $this->getDoctrine()->getRepository(User::class)
            ->OrderByCustom($orderBy[0], $orderBy[1]);

        return $this->render('admin/users/admin_users_index.html.twig', [
            'title' => 'Utilisateurs',
            'users' => $users
        ]);
    }


    /**
     * @Route("/delete/users/}", name="admin_users_delete")
     */
    public function deleteAction()
    {
        dump('coucou'); die;
    }
}