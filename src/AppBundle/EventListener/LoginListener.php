<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Donation;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use DateTime;

class LoginListener
{
    /**
     * Pour pouvoir ajouter des dons au panier sans être connecté en tant qu'utilisateur,
     * nous devons créer un cookie afin de mémoriser à qu'elle machine appartiennent les dons
     * (Entity/Donation/cookieId)
     * Si l'utilisateur se connecte après avoir ajouté des dons à son panier, il faut pouvoir
     * identifier ces dons avec l'utilisateur et non plus avec le cookie
     * On crée pour cela un service qui va écouter l'évènement 'security.interactive_login'
     * (ou l'authentification d'un uttilisateur)
     * 'onLogin' va ainsi déclencher la méthode 'fromCookieToUser' qui elle se chargera de
     * définir l'utilisateur lié à la donation et de redéfinir $cookieId = null
     * Pareil pour une registration avec 'onRegistration'
     *
     *
     * NB: Si une donation $cookie pour une association existe déjà dans le panier $user
     * on remplace seulement la valeur de $amount de celle-ci
     */


    /**
     * @var RegistryInterface
     */
    private $entityManager;


    /**
     * LoginListener constructor.
     * @param RegistryInterface $entityManager
     * Injection de la dépendance 'RegistryInterface'
     */
    public function __construct(RegistryInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param InteractiveLoginEvent $event
     * @throws \Exception
     * Injection de la dépendance 'InteractiveLoginEvent'
     */
    public function onLogin(InteractiveLoginEvent $event)
    {
        // Récupère les variables $user et $id_cookie
        $user = $event->getAuthenticationToken()->getUser();
        $id_cookie = $event->getRequest()->cookies->get('associables_basket');

        $this->fromCookieToUser($user, $id_cookie);
    }


    /**
     * @param FilterUserResponseEvent $event
     * @throws \Exception
     * Injection de la dépendance 'FilterUserResponseEvent'
     */
    public function onRegistration(FilterUserResponseEvent $event)
    {
        // Récupère les variables $user et $id_cookie
        $user = $event->getUser();
        $id_cookie = $event->getRequest()->cookies->get('associables_basket');
        
        $this->fromCookieToUser($user, $id_cookie);
    }


    /**
     * @param $user
     * @param $id_cookie
     * @throws \Exception
     *
     * Remplace le cookieId par un identifiant Utilisateur pour chaque donations au Panier
     */
    private function fromCookieToUser($user, $id_cookie)
    {
        // Recherche d'éventuelles donations en Panier, liées au cookieId
        $donsCookieBasket = $this->entityManager->getRepository(Donation::class)
            ->findBy(['cookieId' => $id_cookie, 'paymentStatus' => Donation::PAY_BASKET]);

        if(!empty($donsCookieBasket))
        {
            // Pour chaque donations trouvées
            foreach($donsCookieBasket as $donationCookie)
            {
                // Verifie que l'utilisateur n'est pas déjà une donation pour la même association en Panier
                $donationUser = $this->entityManager->getRepository(Donation::class)
                    ->existingBasketDonation($donationCookie->getAssos(),$user);

                // Si une telle 'donationUser' existe
                if($donationUser)
                {
                    // Remplace le montant par celui de 'donationCookie' et actualise le DateTime
                    $donationUser->setAmount($donationCookie->getAmount())->setCreatedAt(new DateTime());
                    $this->entityManager->getManager()->persist($donationUser);

                    // Supprime la 'donationCookie'
                    $this->entityManager->getManager()->remove($donationCookie);

                } else {
                    // Sinon, 'donationCookie' hérite de l'entité '$user' ($cookieId devient null)
                    $donationCookie->setUser($user)->setCookieId(null)->setCreatedAt(new DateTime());
                    $this->entityManager->getManager()->persist($donationCookie);
                }
            }
            $this->entityManager->getManager()->flush();
        }
    }
}
