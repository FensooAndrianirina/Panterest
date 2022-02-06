<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;

    private $security;

    public function __construct( Security $security,  UrlGeneratorInterface $urlGenerator)
    {
        $this ->security = $security;
        $this->urlGenerator = $urlGenerator;
    }
    
    public function onLogoutEvent(LogoutEvent $event)
    {
        $event->getRequest()->getSession()->getFlashBag()->add(
            'success',
            $this->security->getUser()->getFullName() . ", you've been successfully logged out!"
        );
        
        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_login')));
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
