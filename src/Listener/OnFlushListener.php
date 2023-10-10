<?php

namespace App\Listener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class OnFlushListener implements EventSubscriber
{
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }
    
    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $user = $this->security->getUser();

        if (in_array('ROLE_DEMO', $user->getRoles()) ){
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'DEMO USER: changes not saved on database');
            $unitOfWork->clear();
        }
    }
    
}