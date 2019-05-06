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
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        if($getCategory != null) {
            // On verifie que la catégorie existe
            $category = $this->getDoctrine()->getRepository(Category::class)
                ->find($getCategory);
            if($category) {
                $associations = $this->getDoctrine()->getRepository(Assos::class)
                    ->getByCategory($getCategory);
            } else {
                return $this->redirectToRoute('associations');
            }
        } else {
            $associations = $this->getDoctrine()->getRepository(Assos::class)->findAll();
        }

        return $this->render('associations.html.twig', [
            'title' => 'associations',
            'categories' => $categories,
            // Comme $getCategory provient de l'url, elle est donc une string,
            // il faut la convertir pour la tester avec category.id
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
}
