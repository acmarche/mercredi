<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Facture\FactureComplement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureComplement|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureComplement|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureComplement[]    findAll()
 * @method FactureComplement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureComplementRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FactureComplement::class);
    }

}
