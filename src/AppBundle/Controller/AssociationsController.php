<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assos;
use AppBundle\Entity\Category;
use AppBundle\Entity\Donation;
use AppBundle\Entity\Review;
use AppBundle\Form\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
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
     * @Route("/associations/{getCategory}", name="associations", defaults={"getCategory": ""})
     */
    public function associationsAction($getCategory)
    {
        // Récupère toutes les catégories pour les afficher dans le menu déroulant
        $categories = $this->getRepo(Category::class)->findAll();

        // Si une catégorie est entrée en paramètre $_GET de l'url
        if($getCategory)
        {
            // Récupère les associations lié a la catégorie grâce à la méthode créé dans 'AssosRepository'
            $associations = $this->getRepo(Assos::class)
                ->findByCategory($getCategory);

            // Si la catégorie n'existe pas ou qu'aucunes associations n'est lié à celle-ci
            // la collection d'objet retourné sera vide
            /*if(!$associations)
            {
                // On redirige sur la page des associations sans le paramètre $getCategory (soit = null)
                $this->addFlash('danger', 'Aucune association n\'est lié à cette catégorie.');
                return $this->redirectToRoute('associations');
            }*/

        } else {

            $associations = $this->getRepo(Assos::class)
                ->findBy([],['name' => 'ASC']);
        }

        // Retourne la vue Twig à la quelle nous avons injecté les variables nécessaire à l'affichage
        return $this->render('associations.html.twig', [
            'title' => 'associations',
            'categories' => $categories,
            'associations' => $associations
        ]);
    }


    /**
     * @Route("/association/{id}", name="association_id")
     */
    public function associationIdAction(Request $request, $id)
    {
        // Récupère l'association via l'id en paramètre
        $association = $this->getRepo(Assos::class)->find($id);

        // Création du formulaire de 'Review' (les avis utilisateur)
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        // Si un avis enregistré via le formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            // Déclare l'utilisateur et l'association concerné
            $review->setUser($this->getUser());
            $review->setAssos($association);

            // Persist / Flush
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            // Redirige sur la même page avec un message de validation
            $this->addFlash('success', 'Votre avis à bien été ajouté, merci!');
            return $this->redirectToRoute('association_id', ['id' => $id]);
        }

        return $this->render('association_id.html.twig', [
            'title' => $association->getName(),
            'association' => $association,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/_ajax/search", name="_ajax_search")
     *
     * Retourne, sous forme de vue html, les associations qui contiennent
     * les caractères entrés en POST 'Request' via la barre de recherche
     * cumulé à la filtration par catégories
     */
    public function _ajaxSearchAction(Request $request)
    {
        // Récupère les lettres tapées dans la bar de recherche et la catégorie
        $search = $request->request->get('search');
        $catg = intval($request->request->get('catg'));

        // la méthode 'findBySearchBar' du répository va chercher les assciations
        // de la catégorie commencent par '$serch'
        $associations = $this->getRepo(Assos::class)
            ->findBySearchBar($search, $catg);

        // Retourne une vue html à la fonction success de la méthode AJAX
        return $this->render('ajax/associations_search.html.twig', [
            'associations' => $associations
        ]);
    }


    /**
     * @Route("/search/donation", name="_search_donation")
     *
     * Une fois affiché via la méthode _ajax_search, la vue 'associations_search.html.twig'
     * ne peut fonctionner avec les scripts d'ajout au panier de la page 'associations'
     * On utilise donc un formulaire POST traditionnel pour créer un nouveau don
     * Redirection vers le panier après validation OU vers les associations en cas d'echec
     */
    public function searchDonationAction(Request $request)
    {
        // $id_asso
        $id_asso = $request->request->get('submit');

        if ($this->getUser())
        {
            // Si l'utilisateur est connecté on récupère son Id
            $id_user = $this->getUser()->getId();
            $id_cookie = null;

        } else {
            // Sinon on utilise la valeur du coockie enregistré
            $id_cookie = $request->cookies->get('associables_basket');
            $id_user = null;
        }

        // Si post['submit'] est défini
        if (!empty($request->request->get('submit'))) {


            // Un try/catch permettra de détecter d'éventuelles erreurs dans le déroulement du code
            // et ainsi de transmettre un message en Front (return false or true)
            try {

                $entityManager = $this->getDoctrine()->getManager();

                // Vérifi si un donation existe dans le panier pour le même utilisateur et la même asso.
                $donationExists = $this->getRepo(Donation::class)
                    ->existingBasketDonation($id_asso, $id_user, $id_cookie);


                if ($donationExists)
                {
                    // Si le don existe déjà, on enregistre le nouveau montant (celui-ci peut être le même)
                    $donationExists->setAmount($request->request->get('amount'));
                    $donationExists->setCreatedAt(new DateTime());
                    $entityManager->persist($donationExists);
                    $entityManager->flush();

                } else {

                    // Sinon :
                    // On instancie une nouvelle donation
                    $donation = new Donation();

                    // Récupère l'objet Assos lié à l'Id de association envoyé en POST['submit']
                    $asso = $this->getRepo(Assos::class)
                        ->find($request->request->get('submit'));

                    // Défini les propriétées de la nouvelle '$donation'
                    $donation
                        ->setAssos($asso)
                        ->setAmount($request->request->get('amount'))
                        ->setPaymentStatus(Donation::PAY_BASKET);

                    if ($this->getUser())
                    {
                        // Défini l'utilisateur
                        $donation->setUser($this->getUser());
                    } else {

                        // Ou la machine ($cookieId)
                        $donation->setCookieId($request->cookies->get('associables_basket'));
                    }

                    $entityManager->persist($donation);
                    $entityManager->flush();
                }

                // Success
                $this->addFlash('success', 'Votre don a bien été ajouté.');
                return $this->redirectToRoute('basket');

            } catch (Exception $e) {

                // Echec
                $this->addFlash('danger', 'Une erreur est survenue, veuillez réessayer.');
                return $this->redirectToRoute('associations');
            }

        } else {

            // Si la route est appellé sans données POST
            return $this->redirectToRoute('associations');
        }
    }
}
