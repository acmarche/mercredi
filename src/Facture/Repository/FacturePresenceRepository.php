<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacturePresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturePresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturePresence[]    findAll()
 * @method FacturePresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FacturePresenceRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const FACTURE = 'facture';
    /**
     * @var string
     */
    private const WITH = 'WITH';
    /**
     * @var string
     */
    private const PRESENCE = 'presence';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FacturePresence::class);
    }

    /**
     * @return FacturePresence[]
     */
    public function findPaye(array $presences): array
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', self::FACTURE, self::WITH)
            ->leftJoin('facture_presence.presence', self::PRESENCE, self::WITH)
            ->addSelect(self::FACTURE, self::PRESENCE)
            ->andWhere('facture_presence.presence IN (:presences)')
            ->setParameter('presences', $presences)
            ->getQuery()->getResult();
    }

    public function findByPresence(Presence $presence): ?FacturePresence
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', self::FACTURE, self::WITH)
            ->addSelect(self::FACTURE)
            ->andWhere('facture_presence.presence = :presence')
            ->setParameter(self::PRESENCE, $presence)
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(FacturePresence $facturePresence): void
    {
        $this->_em->remove($facturePresence);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(FacturePresence $facturePresence): void
    {
        $this->_em->persist($facturePresence);
    }
}
