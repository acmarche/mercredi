<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Facture\FactureReduction;
use AcMarche\Mercredi\Facture\FactureInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureReduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureReduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureReduction[]    findAll()
 * @method FactureReduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureReductionRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FactureReduction::class);
    }

    /**
     * @return array|FactureReduction[]
     */
    public function findByFacture(FactureInterface $facture): array
    {
        return $this->createQueryBuilder('facture_reduction')
            ->andWhere('facture_reduction.facture = :fact')
            ->setParameter('fact', $facture)
            ->getQuery()->getResult();
    }
}
