<?php

namespace AcMarche\Mercredi\Migration;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Paiement;
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

}
