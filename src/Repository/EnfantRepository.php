<?php

namespace AcMarche\Mercredi\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Enfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfant[]    findAll()
 * @method Enfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnfantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enfant::class);
    }


    public function remove(Enfant $enfant)
    {
        $this->_em->remove($enfant);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Enfant $enfant)
    {
        $this->_em->persist($enfant);
    }
}
