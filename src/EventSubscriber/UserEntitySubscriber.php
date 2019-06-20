<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
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

    /**
     * 新規ユーザー登録時にパスワードをハッシュ化します
     * ※ViewEventはSymfony4.3以上で動作します
     *
     * @param ViewEvent $event
     */
    public function onKernelView(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $contentType = $event->getRequest()->getContentType();

        if(Request::METHOD_POST !== $method || "json" !== $contentType) {
            return;
        }

        if($entity instanceof User) {
            $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPassword()));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['onKernelView', EventPriorities::PRE_VALIDATE],
        ];
    }
}
