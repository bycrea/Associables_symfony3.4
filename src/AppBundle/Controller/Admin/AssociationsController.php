<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Category;
use AppBundle\Form\AssociationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class AssociationsController extends Controller
{
    /**
     * @param $entity
     * @return \Doctrine\Common\Persistence\ObjectRepository
     *
     * Function getRepo() permet de simplifier le code pour accéder au repository d'une entité
     */
    public function getRepo($entity)
    {
        return $this->getDoctrine()->getRepository($entity);
    }


    /**
     * ******** READ *********
     *
     * @Route("/associations/{getCategory}", name="admin_associations", defaults={"getCategory": ""})
     * @param $getCategory
     * @return Response
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
            if(!$associations)
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
        // Crée une nouvelle entity Assos
        $association = new Assos();
        // Création du formulaire sur le model de Class AssociationType
        $form = $this->createForm(AssociationType::class, $association);

        $form->handleRequest($request);

        // Si le formaulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {

            // Si une image est jointe formulaire
            if($image = $association->getImage())
            {
                // Fabrique un nom unique suivi de l'extention de l'image
                $imageName = uniqid().'.'.$image->guessExtension();

                try {
                    // Déplace le fichier image dans le répertoire indiqué en paramètre de symfony
                    $image->move($this->getParameter('images_dir'), $imageName);

                } catch (FileException $e) {

                    // Si une erreur se produit, on averti l'utilisateur
                    $this->addFlash('danger', 'Erreur de téléchargement');
                    return $this->redirectToRoute('admin_associations_create');
                }

                // La propriété 'image' de notre entité 'Assos' attend une String (soit un Varchar(255))
                // Elle reçois donc uniquement le nom du fichier image fabriqué plus haut
                $association->setImage($imageName);
            }

            // Transmition en BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();

            // Retourne un message de validation
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
        // Récupère l'association que l'on souhaite modifié grâce à l'id passé en paramètre
        $association = $this->getRepo(Assos::class)->find($id);

        // On récupère la propriété 'image' de notre entité pour qu'elle ne soit pas perdu
        $oldImage = $association->getImage();
        // On n'écrase la propriété 'image' car le formulaire attend un 'fichier' ou 'null'.
        $association->setImage(null);

        // Création du formulaire sur le model de Class AssociationType
        $form = $this->createForm(AssociationType::class, $association);

        $form->handleRequest($request);

        // Si le formaulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {

            // Si une image est jointe formulaire
            if($image = $association->getImage())
            {
                // Fabrique un nom unique suivi de l'extention de l'image
                $imageName = uniqid().'.'.$image->guessExtension();

                try {
                    // Déplace le fichier image dans le répertoire indiqué en paramètre de symfony
                    $image->move($this->getParameter('images_dir'), $imageName);

                    // Supprime l'ancien fichier grâce au nom récupéré plus haut '$oldImage'
                    $file = new Filesystem();
                    $file->remove($this->getParameter('images_dir').$oldImage);

                } catch (FileException $e) {

                    // Si une erreur se produit, on averti l'utilisateur
                    $this->addFlash('danger', 'Erreur de téléchargement');
                    return $this->redirectToRoute('admin_associations_edit', ['id' => $id]);
                }

                // La propriété 'image' de notre entité 'Assos' attend une String (soit un Varchar(255))
                // Elle reçois donc uniquement le nom du fichier image fabriqué plus haut
                $association->setImage($imageName);

            } else {

                // Si aucune image n'est envoyé dans le formulaire,
                // on remet notre ancienne image (soit le nom unique de l'ancien fichier).
                $association->setImage($oldImage);
            }

            // Transmition en BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();

            // Retourne un message de validation
            $this->addFlash('success', $association->getName().' à bien été modifié.');
            return $this->redirectToRoute('admin_associations');
        }

        return $this->render('admin/associations/admin_associations_edit.html.twig', [
            'title' => 'Edition Assos Admin',
            'form' => $form->createView()
        ]);
    }


    /**
     * ******** DELETE *********
     *
     * @Route("/delete/associations/{id}", name="admin_associations_delete")
     */
    public function deleteAction($id)
    {
        // Récupère l'entité que l'on souhaite supprimer grâce à l'id en paramètre
        $association = $this->getRepo(Assos::class)->find($id);

        try {
            // Supprime l'entité
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($association);
            $entityManager->flush();

            // Retourne un message de validation
            $this->addFlash('success', 'L\'association a bien été supprimé.');
            return $this->redirectToRoute('admin_associations');

        } catch (Exception $e) {

            // Retourne un message d'erreur en cas d'échec
            $this->addFlash('danger', 'L\'association n\'a pas pu être supprimé.');
            return $this->redirectToRoute('admin_associations');
        }
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
        // Récupère les caractères à 'chercher' en paramètre ainsi que la catégorie
        $search = $request->request->get('search');
        $catg = intval($request->request->get('catg'));

        // La méthode findBySearchBar() lance une requête DQL de la Class AssosRepository
        // avec en paramètre les caractères et la catégorie à rechercher
        $associations = $this->getRepo(Assos::class)->findBySearchBar($search, $catg);

        // Retourne une vue Twig qui viendra remplacer la vue actuelle (méthode Ajax::success)
        return $this->render('ajax/admin_associations_search.html.twig', [
            'associations' => $associations
        ]);
    }
}
