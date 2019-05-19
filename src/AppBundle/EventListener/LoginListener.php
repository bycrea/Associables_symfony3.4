<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Donation;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    /**
     * Pour pouvoir ajouter des dons au panier sans être connecté en tant qu'utilisateur,
     * nous devons créer un cookie afin de mémoriser à qu'elle machine appartiennent les dons
     * (Entity/Donation/cookieId)
     * Si l'utilisateur se connecte après avoir ajouté des dons à son panier, il faut pouvoir
     * identifier ces dons à l'utilisateur et non plus au cookie
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
     */
    public function __construct(RegistryInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InteractiveLoginEvent $event
     * @throws \Exception
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
     */
    private function fromCookieToUser($user, $id_cookie)
    {
        // Récupère les donations liées au cookie
        $donsCookieBasket = $this->entityManager->getRepository(Donation::class)
            ->findBy(['cookieId' => $id_cookie, 'paymentStatus' => Donation::PAY_BASKET]);

        if(!empty($donsCookieBasket))
        {
            foreach($donsCookieBasket as $donationCookie)
            {
                // Récupère une donation liée à l'utilisateur maintenant connecté
                // et l'association de la 'donationCookie' concernée dans la boucle
                $donationUser = $this->entityManager->getRepository(Donation::class)
                    ->findOneBy([
                        'user' => $user,
                        'assos' => $donationCookie->getAssos(),
                        'paymentStatus' => Donation::PAY_BASKET
                    ]);

                // Si une telle 'donationUser' existe :
                if($donationUser)
                {
                    // On remplace le montant par celui de la 'donationCookie' + new DateTime
                    $donationUser->setAmount($donationCookie->getAmount());
                    $donationCookie->setCreatedAt(new \DateTime());
                    $this->entityManager->getManager()->persist($donationUser);

                    // On supprime la 'donationCookie'
                    $this->entityManager->getManager()->remove($donationCookie);
                } else {
                    // Sinon on efface le 'cookieId' de la 'donationCookie'
                    // qui devient une 'donationUser' + new DateTime
                    $donationCookie->setUser($user);
                    $donationCookie->setCookieId(null);
                    $donationCookie->setCreatedAt(new \DateTime());
                    $this->entityManager->getManager()->persist($donationCookie);
                }
            }

            $this->entityManager->getManager()->flush();
        }
    }
}