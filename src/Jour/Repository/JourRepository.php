<?php

namespace AcMarche\Mercredi\Jour\Repository;

use AcMarche\Mercredi\Entity\Jour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jour[]    findAll()
 * @method Jour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jour::class);
    }

    public function remove(Jour $jour)
    {
        $this->_em->remove($jour);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Jour $jour)
    {
        $this->_em->persist($jour);
    }


}
