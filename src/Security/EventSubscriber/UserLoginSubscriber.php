<?php

namespace AcMarche\Mercredi\Security\EventSubscriber;

use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * bin/console debug:event-dispatcher
 */
final class UserLoginSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [LoginSuccessEvent::class => 'onLoginSuccess'];
    }

    public function onLoginSuccess(LoginSuccessEvent $event)
    {
        /**
         * @var User $user
         */
        $user = $event->getUser();
        $user->setLastLogin(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
