<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Donation;
use Doctrine\ORM\EntityManagerInterface;
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
    // et TokenStorageInterface pour la méthodes $tonken ->getToken->getUser
    public function __construct(RegistryInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    // Méthode inhérente à Twig pour appeler les fonctions créés pour Twig
    public function getFunctions()
    {
        return [
            new TwigFunction('basket_total', [$this, 'getBasketTotal'])
        ];
    }

    // Fonctions Twig personalisées
    // Récupère le nombre de dons dans le panier
    public function getBasketTotal (Request $request)
    {
        $id_cookie = null;
        $id_user = null;

        $token = $this->tokenStorage->getToken();

        if(\is_object($token->getUser()))
        {
            $id_user = $token->getUser()->getId();
        } else {
            $id_cookie = $request->cookies->get('associables_basket');
        }

        $total = $this->entityManager->getRepository(Donation::class)
            ->getBasketTotal($id_user, $id_cookie);

        return $total;
    }
}