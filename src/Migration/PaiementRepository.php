<?php

namespace AcMarche\Mercredi\Migration;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Paiement;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @deprecated  for migration
 *
 * @method Paiement|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]|null findAll()
 * @method Paiement[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    /**
     * @param Tuteur $tuteur
     * @return array|Paiement[]
     */
    public function findByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('paiement')
            ->andWhere('paiement.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->addOrderBy('paiement.date_paiement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Paiement[]
     */
    public function getByEnfantTuteur(Tuteur $tuteur, Enfant $enfant, int $year = null):array
    {
        return $this->createQueryBuilder('paiement')
            ->andWhere('paiement.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andWhere('paiement.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('paiement.date_paiement LIKE :year')
            ->setParameter('year', $year.'%')
            ->addOrderBy('paiement.date_paiement', 'DESC')
            ->getQuery()
            ->getResult();

    }

}
