<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;

class RequestEventListener
{
    /**
     * Pour pouvoir ajouter des dons au panier sans être connecté en tant qu'utilisateur,
     * nous devons créer un cookie afin de mémoriser à qu'elle machine appartiennent les dons
     * (Entity/Donation/cookieId)
     * On crée pour cela un service RequestEventListener implémenté dans 'services.yml'
     * A chaque fin de chargement de page (kernel.response) celui-ci vérifie qu'un cookie existe
     * S'il n'existe pas il crée un cookie unique.
     */

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // FilterResponseEvent met à disposition la méthode 'getRequest'
        // qui permet ensuite de récuperer les cookies.
        $cookie = $event->getRequest()->cookies;

        // Si l'objet $cookie ne possède pas déjà notre cookie 'assocaibles_basket'
        // On utlise la méthode 'getResponse' pour injecter un cookie au headers HTTP de notre application
        if(!$cookie->has('associables_basket'))
        {
            $responce = $event->getResponse();
            $responce->headers->setCookie(new Cookie('associables_basket', md5(uniqid())));
        }
    }
}
