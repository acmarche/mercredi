<?php

namespace AcMarche\Mercredi\Repository;

use AcMarche\Mercredi\Entity\Reduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reduction[]    findAll()
 * @method Reduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reduction::class);
    }

    public function remove(Reduction $reduction)
    {
        $this->_em->remove($reduction);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Reduction $reduction)
    {
        $this->_em->persist($reduction);
    }

}
