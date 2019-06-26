<?php


namespace App\Doctrine\EventSubscriber;


use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $objectManager = $eventArgs->getObjectManager();

        if($entity instanceof User) {
            if($entity->getPlainPassword()) {
                $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword()));
            }

            $entity->setRoles($entity->getRoles());

            $userRepository = $objectManager->getRepository(User::class);
            $secretKey = $userRepository->getUniqueSecretKey();

            $entity->setSecretKey($secretKey);
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $objectManager = $eventArgs->getObjectManager();

        if($entity instanceof User) {
            if($entity->getPlainPassword()) {
                $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword()));
            }

            $userRepository = $objectManager->getRepository(User::class);
            $secretKey = $userRepository->getUniqueSecretKey();

            $entity->setSecretKey($secretKey);
        }
    }
}