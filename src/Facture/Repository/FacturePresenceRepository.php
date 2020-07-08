<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FacturePresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturePresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturePresence[]    findAll()
 * @method FacturePresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturePresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacturePresence::class);
    }

    /**
     * @return FacturePresence[]
     */
    public function findPaye(array $presences): array
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', 'facture', 'WITH')
            ->leftJoin('facture_presence.presence', 'presence', 'WITH')
            ->addSelect('facture', 'presence')
            ->andWhere('facture_presence.presence IN (:presences)')
            ->setParameter('presences', $presences)
            ->getQuery()->getResult();
    }

    public function findByPresence(Presence $presence): ?FacturePresence
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', 'facture', 'WITH')
            ->addSelect('facture')
            ->andWhere('facture_presence.presence = :presence')
            ->setParameter('presence', $presence)
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(FacturePresence $facturePresence)
    {
        $this->_em->remove($facturePresence);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(FacturePresence $facturePresence)
    {
        $this->_em->persist($facturePresence);
    }
}
