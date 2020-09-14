<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureAccueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureAccueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureAccueil[]    findAll()
 * @method FactureAccueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FactureAccueilRepository extends ServiceEntityRepository
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
    private const ACCUEIL = 'accueil';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FactureAccueil::class);
    }

    /**
     * @param Accueil[] $accueils
     *
     * @return FactureAccueil[]
     */
    public function findPaye(array $accueils): array
    {
        return $this->createQueryBuilder('facture_accueil')
            ->leftJoin('facture_accueil.facture', self::FACTURE, self::WITH)
            ->leftJoin('facture_accueil.accueil', self::ACCUEIL, self::WITH)
            ->addSelect(self::FACTURE, self::ACCUEIL)
            ->andWhere('facture_accueil.accueil IN (:accueils)')
            ->setParameter('accueils', $accueils)
            ->getQuery()->getResult();
    }

    public function findByAccueil(Accueil $accueil): ?FactureAccueil
    {
        return $this->createQueryBuilder('facture_accueil')
            ->leftJoin('facture_accueil.facture', self::FACTURE, self::WITH)
            ->addSelect(self::FACTURE)
            ->andWhere('facture_accueil.accueil = :accueil')
            ->setParameter(self::ACCUEIL, $accueil)
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(FactureAccueil $factureAccueil): void
    {
        $this->_em->remove($factureAccueil);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(FactureAccueil $factureAccueil): void
    {
        $this->_em->persist($factureAccueil);
    }
}
