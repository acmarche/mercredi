<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FactureAccueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureAccueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureAccueil[]    findAll()
 * @method FactureAccueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureAccueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureAccueil::class);
    }

    /**
     * @return FactureAccueil[]
     */
    public function findPaye(array $accueils): array
    {
        return $this->createQueryBuilder('facture_accueil')
            ->leftJoin('facture_accueil.facture', 'facture', 'WITH')
            ->leftJoin('facture_accueil.accueil', 'accueil', 'WITH')
            ->addSelect('facture', 'accueil')
            ->andWhere('facture_accueil.accueil IN (:accueils)')
            ->setParameter('accueils', $accueils)
            ->getQuery()->getResult();
    }

    public function findByAccueil(Accueil $accueil): ?FactureAccueil
    {
        return $this->createQueryBuilder('facture_accueil')
            ->leftJoin('facture_accueil.facture', 'facture', 'WITH')
            ->addSelect('facture')
            ->andWhere('facture_accueil.accueil = :accueil')
            ->setParameter('accueil', $accueil)
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
