<?php

namespace AcMarche\Mercredi\Enfant\Repository;

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

    /**
     * @param $keyword
     * @return Enfant[]
     */
    public function search($keyword): array
    {
        $qb = $this->createQueryBuilder('enfant')
            ->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->addOrderBy('enfant.nom', 'ASC')
            ->getQuery()->getResult();

        return $qb;
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

    /**
     * @return Enfant[]
     */
    public function findOrphelins()
    {
        $qb = $this->createQueryBuilder('enfant')
            ->andWhere('enfant.relations IS EMPTY')
            ->getQuery()->getResult();

        return $qb;
    }

}
