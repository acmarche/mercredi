<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method PlaineGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineGroupe[]    findAll()
 * @method PlaineGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaineGroupe::class);
    }

    public function findPlaineOpen(): ?PlaineGroupe
    {
        return $this->createQueryBuilder('plaine')
            ->andWhere('plaine.inscriptionOpen = 1')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(PlaineGroupe $plaineGroupe)
    {
        $this->_em->remove($plaineGroupe);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(PlaineGroupe $plaineGroupe)
    {
        $this->_em->persist($plaineGroupe);
    }

    public function getQbForListing(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('plaine')
            ->orderBy('plaine.nom', 'ASC');

        return $qb;
    }
}