<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(TokenStorageInterface $tokenStorage, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->tokenStorage = $tokenStorage;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getAuthenticatedUser(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $contentType = $event->getRequest()->getContentType();

        if(Request::METHOD_POST !== $method || "application/json" !== $contentType) {
            return;
        }

        if($entity instanceof User) {
            $roles = $entity->getRoles();
            $entity->setRoles($roles);
            $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPassword()));
        }

        if($entity instanceof Book) {
            /** @var  User */
            $user = $this->tokenStorage->getToken()->getUser();

            $entity->setUser($user);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_VALIDATE],
        ];
    }
}
