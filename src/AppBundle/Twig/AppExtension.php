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

    // Un service symfony appelle le constructeur automatiquement et applique l'injection de dépendance.
    // Ici nous injectons RegistryInterface pour avoir access aux méthodes de $entityManager
    // et TokenStorageInterface pour la méthodes $user = $this->tokenStorage->getToken()->getUser();
    public function __construct(RegistryInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    // Méthode inhérente à Twig pour appeler les fonctions créés pour Twig
    public function getFunctions()
    {
        return [
            new TwigFunction('getBasketTotal', [$this, 'getBasketTotal'])
        ];
    }

    // Fonctions Twig personalisées
    // Récupère le nombre de dons dans le panier
    public function getBasketTotal (Request $request)
    {
        // Initialise les variables
        $id_cookie = null;
        $id_user = null;

        // Récupère l'objet Token grâce à l'injection de dépendances
        // et récupère l'utilisateur grâce à la méthode getUser
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

        return $basketTotal['quantity'];
    }
}