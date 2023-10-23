<?php

namespace AcMarche\Mercredi\Doctrine\EventSubscriber;

use AcMarche\Mercredi\Utils\PropertyUtil;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsDoctrineListener(Events::prePersist)]
final class UserAddSubscriber
{
    public function __construct(
        private Security $security,
        private PropertyUtil $propertyUtil
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            //   Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $object = $lifecycleEventArgs->getObject();
        if (!$this->propertyUtil->getPropertyAccessor()->isWritable($object, 'userAdd')) {
            return;
        }

        $this->setUserAdd($object);
    }

    private function setUserAdd(object $entity): void
    {
        //for loading fixtures
        if ($entity->getUserAdd()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            // throw new Exception('You must be login');
        }
        if ($user instanceof UserInterface) {
            $identifier = $user->getUserIdentifier();
        } else {
            $identifier = 'usernotfound';
        }
        $entity->setUserAdd($identifier);
    }
}
