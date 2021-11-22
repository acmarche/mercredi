<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Facture\FactureInterface;
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
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FacturePresence::class);
    }

    /**
     * @param array $presenceIds
     * @param string $type
     * @return array|FacturePresence[]
     */
    public function findByIdsAndType(array $presenceIds, string $type): array
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', 'facture', 'WITH')
            ->addSelect('facture')
            ->andWhere('facture_presence.presenceId IN (:presences)')
            ->setParameter('presences', $presenceIds)
            ->andWhere('facture_presence.objectType = :type')
            ->setParameter('type', $type)
            ->getQuery()->getResult();
    }

    public function findByIdAndType(int $presenceId, string $type): ?FacturePresence
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', 'facture', 'WITH')
            ->addSelect('facture')
            ->andWhere('facture_presence.presenceId = :presence')
            ->setParameter('presence', $presenceId)
            ->andWhere('facture_presence.objectType = :type')
            ->setParameter('type', $type)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $type
     * @return array|FacturePresence[]
     */
    public function findByFactureAndType(FactureInterface $facture, string $type): array
    {
        return $this->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', 'facture', 'WITH')
            ->leftJoin('facture_presence.reduction', 'reduction', 'WITH')
            ->addSelect('facture', 'reduction')
            ->andWhere('facture_presence.facture = :fact')
            ->setParameter('fact', $facture)
            ->andWhere('facture_presence.objectType = :type')
            ->setParameter('type', $type)
            ->getQuery()->getResult();
    }

    public function findByPresence(Presence $presence): ?FacturePresence
    {
        return $this->findByIdAndType($presence->getId(), FactureInterface::OBJECT_PRESENCE);
    }

    public function findByAccueil(Accueil $accueil): ?FacturePresence
    {
        return $this->findByIdAndType($accueil->getId(), FactureInterface::OBJECT_ACCUEIL);
    }

    public function findByPlaine(Plaine $plaine): ?FacturePresence
    {
        return $this->findByIdAndType($plaine->getId(), FactureInterface::OBJECT_PLAINE);
    }
}
