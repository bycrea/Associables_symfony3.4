<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssociationsController extends Controller
{
    /**
     * @Route("/associations/{getCategory}", name="associations")
     */
    public function associationsAction(Request $request, $getCategory = null)
    {
        // Récupère toutes les catégories pour les afficher dans le menu déroulant
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        // Si une catégorie est entrée en paramètre $_GET de l'url
        if($getCategory != null)
        {
            // Récupère les associations lié a la catégorie grâce à la méthode créé dans 'AssosRepository'
            $associations = $this->getDoctrine()->getRepository(Assos::class)
                ->getByCategory($getCategory);

            // Si la catégorie n'existe pas ou qu'aucunes associations n'est lié à celle-ci
            // la collection d'objet retourné sera vide
            if(empty($associations))
            {
                // On redirige sur la page des associations sans le paramètre $getCategory (soit = null)
                $this->addFlash('danger', 'Aucune association n\'est lié à cette catégorie.');
                return $this->redirectToRoute('associations');
            }

        } else {

            // Récupère toutes les associations, toutes catégories confondues
            $associations = $this->getDoctrine()->getRepository(Assos::class)->findAll();
        }

        // Retourne la vue Twig à la quelle nous avons injecté les variables nécessaire à l'affichage
        return $this->render('associations.html.twig', [
            'title' => 'associations',
            'categories' => $categories,
            // '$getCategory' provenant du $_GET de l'url
            // il faut la convertir en 'int' pour la tester avec category.id
            'getCategory' => intval($getCategory),
            'associations' => $associations
        ]);
    }


    /**
     * @Route("/association/{id}", name="association_id")
     */
    public function associationIdAction($id)
    {
        $association = $this->getDoctrine()->getRepository(Assos::class)->find($id);

        return $this->render('association_id.html.twig', [
            'title' => $association->getName(),
            'association' => $association
        ]);
    }


    /**
     * @Route("/_ajax/search", name="_ajax_search")
     */
    public function _ajaxSearchAction(Request $request)
    {
        $search = $request->request->get('search');

        $associations = $this->getDoctrine()->getRepository(Assos::class)
            ->findBySearchBar($search);


        return $this->render('associations_search.html.twig', [
            'associations' => $associations
        ]);
    }

}
