<?php

namespace AcMarche\Mercredi\Doctrine\EventSubscriber;

use AcMarche\Mercredi\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Stringable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsDoctrineListener(Events::preRemove)]
class DeleteRecordSubscriber
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->security->getUser();

        if ($user instanceof UserInterface) {
            $identifier = $user->getUserIdentifier();
        } else {
            $identifier = 'user not found';
        }

        $name = " ";
        if ($entity instanceof Stringable) {
            $name = $entity->__toString();
        }

        $history = new History('delete record '.$entity->getId());
        $history->message = "suppression de ".$name." par ".$identifier." object : ".$entity::class;
        $history->count = 1;
        $history->created_at = new \DateTime();
        $this->entityManager->persist($history);
        try {
            $this->entityManager->flush();
        } catch (\Exception $exception) {
        }
    }
}