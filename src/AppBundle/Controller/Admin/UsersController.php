<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Donation;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends Controller
{
    /**
     * @Route("/users/{filter}", name="admin_users", defaults={"filter": "id"})
     */
    public function indexAction($filter)
    {
        // Grâce au $filter, on détermine OrderBy[] pour l'envoyer dans la requête DQL
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

        // Récupère les utilisateurs en fonction du filtre grâce à la méthode 'OrderByCustom'
        $users = $this->getDoctrine()->getRepository(User::class)
            ->OrderByCustom($orderBy[0], $orderBy[1]);

        return $this->render('admin/users/admin_users_index.html.twig', [
            'title' => 'Utilisateurs',
            'users' => $users
        ]);
    }


    /**
     * @Route("/roles/users/{id}", name="admin_users_roles")
     */
    public function rolesAction($id)
    {
        // Récupère l'entity User via l'id
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        // Si le 'ROLE_ADMIN' existe pour cet Utilisateur
        if(false !== array_search('ROLE_ADMIN', $user->getRoles(), true))
        {
            // On le retire
            $user->removeRole('ROLE_ADMIN');

        } else {

            // Sinon on le rajoute
            $user->addRole('ROLE_ADMIN');
        }

        // Persist -> Flush
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }


    /**
     * @Route("/delete/users}", name="admin_users_delete")
     */
    public function deleteAction()
    {
        dump('Pas eu le temps de gérer ça... déso.'); die;
    }
}