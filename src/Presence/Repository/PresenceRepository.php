<?php

namespace AcMarche\Mercredi\Presence\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    /**
     * @param Enfant $enfant
     * @return Jour[]
     */
    public function findDaysRegisteredByEnfant(Enfant $enfant)
    {
        $presences = $this->findPresencesByEnfant($enfant);
        $jours = [];
        foreach ($presences as $presence) {
            $jours[] = $presence->getJour();
        }

        return $jours;
    }

    /**
     * @param Enfant $enfant
     * @return Presence[]
     */
    public function findPresencesByEnfant(Enfant $enfant)
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @param Enfant $enfant
     * @param Jour $jour
     * @return Presence
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function exist(Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQueryBuilder('presence')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(Presence $presence)
    {
        $this->_em->remove($presence);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Presence $presence)
    {
        $this->_em->persist($presence);
    }

}
