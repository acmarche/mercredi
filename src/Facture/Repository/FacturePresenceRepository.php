<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Facture\FactureInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
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
     * @return array|FacturePresence[]
     */
    public function findByIdsAndType(array $presenceIds, string $type): array
    {
        return $this
            ->createQbl()
            ->andWhere('facture_presence.presenceId IN (:presences)')
            ->setParameter('presences', $presenceIds)
            ->andWhere('facture_presence.objectType = :type')
            ->setParameter('type', $type)
            ->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByIdAndType(int $presenceId, ?string $type): ?FacturePresence
    {
        $qbl = $this
            ->createQbl()
            ->andWhere('facture_presence.presenceId = :presence')
            ->setParameter('presence', $presenceId);

        if ($type) {
            $qbl
                ->andWhere('facture_presence.objectType = :type')
                ->setParameter('type', $type);
        }

        return $qbl->getQuery()->getOneOrNullResult();
    }

    /**
     * @return FacturePresence[]
     */
    public function findByFactureAndType(FactureInterface $facture, string $type): array
    {
        return $this
            ->createQbl()
            ->andWhere('facture_presence.facture = :fact')
            ->setParameter('fact', $facture)
            ->andWhere('facture_presence.objectType = :type')
            ->setParameter('type', $type)
            ->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByPresence(
        PresenceInterface $presence,
        string $type = FactureInterface::OBJECT_PRESENCE,
    ): ?FacturePresence {
        return $this->findByIdAndType($presence->getId(), $type);
    }

    public function findByAccueil(Accueil $accueil): ?FacturePresence
    {
        return $this->findByIdAndType($accueil->getId(), FactureInterface::OBJECT_ACCUEIL);
    }

    /**
     * @return FacturePresence[]
     */
    public function findByReduction(Reduction $reduction): array
    {
        return $this
            ->createQbl()
            ->andWhere('facture_presence.reduction = :reduction')
            ->setParameter('reduction', $reduction)
            ->getQuery()->getResult();
    }

    private function createQbl(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('facture_presence')
            ->leftJoin('facture_presence.facture', 'facture', 'WITH')
            ->leftJoin('facture_presence.reduction', 'reduction', 'WITH')
            ->addSelect('facture', 'reduction');
    }

}
