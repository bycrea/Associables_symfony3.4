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

    /**
     * @var RegistryInterface
     */
    private $entityManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    // Quand on crée un service symfony, son constructeur est appelé automatiquement
    // Et applique l'injection de dépendence.
    // L'injection de dépendence c'est : Injecter les service dont on va avoir besoin dans le service
    // que l'on vient de créer.
    // On peut ensuite utiliser les méthodes du service injecté dans notre service (class) actuel
    // Après avoir ajouté la dépendence souhaité en paramètre, on initialise le field en cliquant sur la variable
    // puis ALT+ENTER
    public function __construct(RegistryInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('basket_total', [$this, 'getBasketTotal'])
        ];
    }

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