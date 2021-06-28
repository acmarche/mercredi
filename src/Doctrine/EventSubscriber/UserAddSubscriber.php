<?php

namespace AcMarche\Mercredi\Doctrine\EventSubscriber;

use AcMarche\Mercredi\Utils\PropertyUtil;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Symfony\Component\Security\Core\Security;

final class UserAddSubscriber implements EventSubscriber
{
    private Security $security;
    private PropertyUtil $propertyUtil;

    public function __construct(Security $security, PropertyUtil $propertyUtil)
    {
        $this->security = $security;
        $this->propertyUtil = $propertyUtil;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            //   Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $object = $lifecycleEventArgs->getObject();
        if (! $this->propertyUtil->getPropertyAccessor()->isWritable($object, 'userAdd')) {
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

        if (null === $user) {
            throw new Exception('You must be login');
        }

        if ($user) {
            $entity->setUserAdd($user->getUsername());
        }
    }
}
