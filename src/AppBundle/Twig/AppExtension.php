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
     * Un Service Symfony appelle le constructeur automatiquement et applique l'injection de dépendance.
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
            new TwigFunction('getBasketInfos', [$this, 'getBasketInfos']),
            new TwigFunction('textSlice', [$this, 'textSlice'])
        ];
    }


    /**
     * @param Request $request
     * @return mixed
     *
     * Fonctions Twig personalisée,
     * Récupère le nombre de dons en Panier et le montant total
     */
    public function getBasketInfos(Request $request)
    {
        // Initialise les variables
        $id_cookie = null; $id_user = null;

        // L'objet Token de la dépendances TokenStorageInterface permet de récupérer l'utilisateur
        $user = $this->tokenStorage->getToken()->getUser();

        if(\is_object($user))
        {
            $id_user = $user->getId();

        } else {
            // Récupère l'id_cookie grâce à la requête HTTP injecté en paramètre
            $id_cookie = $request->cookies->get('associables_basket');
        }

        $basketTotal = $this->entityManager->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        // Retourne un tableau des résultats (index : 'quantity' , 'amount')
        return $basketTotal;
    }


    /**
     * @param $text
     * @param $length
     * @return bool|string
     *
     * Fonctions Twig personalisée,
     * Retourne un texte limité à un nombre de de caractères prédéfini avec $length
     */
    public function textSlice($text, $length)
    {
        // Si la taille du $text est supérieur à $length
        if(strlen($text) > $length)
        {
            // Limite la taille du $text à $length + ...
            $sliceText = substr($text, 0, $length).'...';
            return $sliceText;

        } else {

            return $text;
        }
    }
}