<?php

namespace AcMarche\Mercredi\Reduction\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Reduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reduction[]    findAll()
 * @method Reduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ReductionRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Reduction::class);
    }
}
