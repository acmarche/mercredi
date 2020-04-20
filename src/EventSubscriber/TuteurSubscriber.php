<?php

namespace AcMarche\Mercredi\EventSubscriber;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class TuteurSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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
        if (!$entity instanceof Tuteur) {
            return;
        }

        $this->setUserAdd($entity);
    }

    private function setUserAdd(Enfant $enfant)
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new \Exception('You must be login');
        }

        if ($user) {
            $enfant->setUserAdd($user->getUsername());
        }
    }

}
