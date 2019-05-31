<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Simplification de la Route pour les méthodes du controller
 * en ajoutant l'annotation ci-dessous pour éviter de nommer les routes
 * en commencant par '/categories'/... à chaque fois
 *
 * @Route("/categories")
 */
class CategoriesController extends Controller
{

    /**
     * Grâce au jQuery, on peut ajouter ou modifier des catégories dans la même page
     * voir : 'admin/categories/admin_categories_index.html.twig'
     *
     * @Route("/", name="admin_categories")
     */
    public function indexAction(Request $request)
    {
        // Si le formulaire d'ajout ou de modification est soumis
        if (!empty($request->request->get('submit'))) {

            // Si un 'edit-id' est transmit, il s'agit d'une modification
            if(!empty($id = $request->request->get('edit-id')))
            {
                // Récupère la catégorie concerné par l'id et on prépare la réponse 'success' correspondante
                $category = $this->getDoctrine()->getRepository(Category::class)
                    ->find($id);
                $this->addFlash('success', 'La categorie a bien été modifié.');

            } else {

                // Sinon on crée une nouvelle catégorie et on prépare la réponse 'success' correspondante
                $category = new Category();
                $this->addFlash('success', 'La categorie a bien été ajouté.');
            }

            // Implémente le nom de la catégorie
            $category->setName($request->request->get('name'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }

        // Récupère toutes les catégories existantes
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/categories/admin_categories_index.html.twig', [
            'title' => 'Categories Admin',
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/delete/{id}", name="admin_categories_delete")
     */
    public function deleteAction($id)
    {
        // Récupère la catégorie correspondante à l'id en paramètre POST venant de l'Ajax
        // $catg_id = $request->request->get('id');
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        // Supprime la catégorie
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        // Prépare le message 'success' correspondant
        $this->addFlash('success', 'La categorie a bien été supprimé.');

        // Redirige vers indexAction
        return $this->redirectToRoute('admin_categories');
    }

}
