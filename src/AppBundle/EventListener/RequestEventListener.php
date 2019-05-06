<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;

class RequestEventListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $cookie = $request->cookies;

        if(!$cookie->has('associables_basket'))
        {
            $responce = $event->getResponse();
            $responce->headers->setCookie(new Cookie('associables_basket', md5(uniqid())));
        }
    }
}