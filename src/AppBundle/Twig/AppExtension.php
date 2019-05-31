<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Donation;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    // Creation d'un service Twig

    /**
     * @var RegistryInterface
     */
    private $entityManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;


    /**
     * AppExtension constructor.
     * @param RegistryInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     *
     * Un service symfony appelle le constructeur automatiquement et applique l'injection de dépendance.
     * Ici nous injectons RegistryInterface pour avoir access aux méthodes de $entityManager
     * et TokenStorageInterface pour la méthodes $user = $this->tokenStorage->getToken()->getUser();
     */
    public function __construct(RegistryInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }


    // Méthode inhérente à Twig pour appeler les fonctions créés pour Twig
    public function getFunctions()
    {
        return [
            new TwigFunction('getBasketQuantity', [$this, 'getBasketQuantity']),
            new TwigFunction('textSlice', [$this, 'textSlice'])
        ];
    }


    /**
     * @param Request $request
     * @return mixed
     *
     * Fonctions Twig personalisée,
     * Récupère le nombre de dons dans le panier
     */
    public function getBasketQuantity(Request $request)
    {
        // Initialise les variables
        $id_cookie = null;
        $id_user = null;

        // Récupère l'objet Token grâce à l'injection de dépendances
        // et récupère l'utilisateur grâce à la méthode getUser
        $user = $this->tokenStorage->getToken()->getUser();

        if(\is_object($user))
        {
            // Récupère l'id user
            $id_user = $user->getId();
        } else {
            // Récupère l'id_cookie grâce à la requête HTTP injecté en paramètre
            $id_cookie = $request->cookies->get('associables_basket');
        }

        $basketTotal = $this->entityManager->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        return $basketTotal['quantity'];
    }


    /**
     * @param $text
     * @param $length
     * @return bool|string
     *
     * Fonctions Twig personalisée,
     * Retourne un texte $text limité à un nombre de de caractères prédéfini avec $length
     */
    public function textSlice($text, $length)
    {
        // Si la taille du $text est supérieur à $length
        if(strlen($text) > $length)
        {
            // On limite la taille du $text à $length + ...
            $sliceText = substr($text, 0, $length).'...';

        } else {

            return $text;
        }

        return $sliceText;
    }
}