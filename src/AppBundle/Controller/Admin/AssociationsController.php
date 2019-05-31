<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Category;
use AppBundle\Form\AssociationType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AssociationsController extends Controller
{
    /**
     * @param $entity
     * @return \Doctrine\Common\Persistence\ObjectRepository
     *
     * Function getRepo() permet de simplifier le code pour accéder à un repository
     */
    public function getRepo($entity)
    {
        return $this->getDoctrine()->getRepository($entity);
    }


    /**
     * ******** READ *********
     *
     * @Route("/associations/{getCategory}", name="admin_associations", defaults={"getCategory": ""})
     */
    public function indexAction($getCategory)
    {
        // Récupère toutes les catégories pour les afficher dans le menu déroulant
        $categories = $this->getRepo(Category::class)->findAll();

        // Si une catégorie est entrée en paramètre $_GET de l'url
        if($getCategory)
        {
            // Récupère les associations lié a la catégorie grâce à la méthode créé dans 'AssosRepository'
            $associations = $this->getRepo(Assos::class)->findByCategory($getCategory);

            // Si la catégorie n'existe pas ou qu'aucunes associations n'est lié à celle-ci
            // la collection d'objet retourné sera vide
            if(empty($associations))
            {
                // On redirige sur la page des associations sans le paramètre $getCategory (soit = null)
                $this->addFlash('danger', 'Aucune association n\'est lié à cette catégorie.');
                return $this->redirectToRoute('admin_associations');
            }

        } else {

            // Récupère toutes les associations, toutes catégories confondues
            $associations = $this->getRepo(Assos::class)->findBy([],['name' => 'ASC']);
        }

        return $this->render('admin/associations/admin_associations_index.html.twig', [
            'title' => 'Associations Admin',
            'categories' => $categories,
            'associations' => $associations
        ]);
    }


    /**
     * ******** CREATE *********
     *
     * @Route("create/associations", name="admin_associations_create")
     */
    public function associationCreateAction(Request $request)
    {

        $association = new Assos();
        $form = $this->createForm(AssociationType::class, $association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $association->getImage();

            if($image)
            {
                $imageName = uniqid().'.'.$image->guessExtension();

                try {

                    $image->move($this->getParameter('images_dir'), $imageName);

                } catch (FileException $e) {

                    $this->addFlash('danger', 'Erreur de téléchargement');
                    return $this->redirectToRoute('admin_associations_create');
                }

                $association->setImage($imageName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();

            $this->addFlash('success', $association->getName().' à bien été ajouté.');
            return $this->redirectToRoute('admin_associations');
        }

        return $this->render('admin/associations/admin_associations_edit.html.twig', [
            'title' => 'Creation Asso Admin',
            'form' => $form->createView()
        ]);
    }


    /**
     * ******** EDIT *********
     *
     * @Route("/edit/associations/{id}", name="admin_associations_edit")
     */
    public function associationEditAction(Request $request, $id)
    {

        $association = $this->getRepo(Assos::class)->find($id);

        $oldImage = $association->getImage();
        $association->setImage(null);
        $form = $this->createForm(AssociationType::class, $association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $association->getImage();

            if($image)
            {
                $imageName = uniqid().'.'.$image->guessExtension();

                try {

                    $path = $this->getParameter('images_dir');

                    $image->move($path, $imageName);

                    $file = new Filesystem();
                    $file->remove($path.$oldImage);

                } catch (FileException $e) {

                    $this->addFlash('danger', 'Erreur de téléchargement');
                    return $this->redirectToRoute('admin_associations_create');
                }

                $association->setImage($imageName);

            } else {

                $association->setImage($oldImage);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();

            $this->addFlash('success', $association->getName().' à bien été modifié.');
            return $this->redirectToRoute('admin_associations');
        }

        return $this->render('admin/associations/admin_associations_edit.html.twig', [
            'title' => 'Edition Assos Admin',
            'form' => $form->createView()
        ]);
    }


    /**
     * ******** DELETE AJAX *********
     *
     * @Route("_ajax/delete/associations", name="admin_ajax_associations_delete")
     */
    public function _ajaxDeleteAction(Request $request)
    {
        $this->addFlash('success', 'je passe par ajax delete');
        return $this->redirectToRoute('admin_associations');

        $id = $request->request->get('id');
        $association = $this->getRepo(Assos::class)->find($id);

        try {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($association);
            $entityManager->flush();

            $this->addFlash('success', 'L\'association a bien été supprimé.');
            return $this->json([
                'status' => true,
                'url' => $this->generateUrl('admin_associations')
            ]);

        } catch (Exception $e) {

            return $this->json([
            'status' => false,
            'message' => 'L\'association ne peut pas être supprimé.'
            ]);
        }
    }


    /**
     * ******** DELETE *********
     *
     * @Route("/delete/associations/{id}", name="admin_associations_delete")
     */
    public function deleteAction($id)
    {
        $association = $this->getRepo(Assos::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($association);
        $entityManager->flush();

        $this->addFlash('success', 'L\'association a bien été supprimé.');
        return $this->redirectToRoute('admin_associations');
    }


    /**
     * ******** RECHERCHE EN AJAX *********
     *
     * @Route("/_ajax/search/associations", name="admin_ajax_search")
     *
     * Retourne, sous forme de vue, les associations qui contiennent
     * les caractères entrés en POST Ajax 'Request' via la barre de recherche
     */
    public function _ajaxSearchAction(Request $request)
    {
        $search = $request->request->get('search');
        $catg = intval($request->request->get('catg'));

        $associations = $this->getRepo(Assos::class)->findBySearchBar($search, $catg);

        return $this->render('ajax/admin_associations_search.html.twig', [
            'associations' => $associations
        ]);
    }
}
