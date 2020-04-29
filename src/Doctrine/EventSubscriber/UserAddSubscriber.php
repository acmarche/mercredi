<?php

namespace AcMarche\Mercredi\Doctrine\EventSubscriber;

use AcMarche\Mercredi\Utils\PropertyUtil;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class UserAddSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var PropertyUtil
     */
    private $propertyUtil;

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

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->propertyUtil->getPropertyAccessor()->isWritable($entity, 'userAdd')) {
            return;
        }

        $this->setUserAdd($entity);
    }

    private function setUserAdd(object $entity)
    {
        //for loading fixtures
        if ($entity->getUserAdd()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception('You must be login');
        }

        if ($user) {
            $entity->setUserAdd($user->getUsername());
        }
    }

}
