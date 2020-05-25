<?php

namespace AcMarche\Mercredi\Ecole\Repository;

use AcMarche\Mercredi\Entity\Ecole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Ecole|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecole|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecole[]    findAll()
 * @method Ecole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecole::class);
    }

    public function remove(Ecole $ecole)
    {
        $this->_em->remove($ecole);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Ecole $ecole)
    {
        $this->_em->persist($ecole);
    }

    public function getQbForListing(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('ecole')
            ->orderBy('ecole.nom', 'ASC');

        return $qb;
    }
}
