<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelView(ViewEvent $event)
    {
        if(!$user = $this->getUser()) {
            return;
        }

        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $contentType = $event->getRequest()->getContentType();

        if(Request::METHOD_POST !== $method || "json" !== $contentType) {
            return;
        }

        if($entity instanceof Order) {
            $entity->setUser($user);
        }
    }

    /**
     * @return UserInterface|null
     */
    private function getUser(): ?UserInterface
    {
        if(!$token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();
        return $user instanceof UserInterface ? $user : null;
    }

    public static function getSubscribedEvents()
    {
        return [
           'kernel.view' => ['onKernelView', EventPriorities::PRE_VALIDATE],
        ];
    }
}
