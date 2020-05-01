<?php

namespace AcMarche\Mercredi\Tuteur\Repository;

use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tuteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tuteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tuteur[]    findAll()
 * @method Tuteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tuteur::class);
    }

    /**
     * @param $keyword
     * @return Tuteur[]
     */
    public function search($keyword): array
    {
        $qb = $this->createQueryBuilder('tuteur')
            ->andWhere('tuteur.nom LIKE :keyword OR tuteur.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->addOrderBy('tuteur.nom', 'ASC')
            ->getQuery()->getResult();

        return $qb;
    }

    /**
     * @return Tuteur[]
     */
    public function findSansEnfants()
    {
        $qb = $this->createQueryBuilder('tuteur')
            ->andWhere('tuteur.relations IS EMPTY')
            ->getQuery()->getResult();

        return $qb;
    }

    public function remove(Tuteur $tuteur)
    {
        $this->_em->remove($tuteur);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Tuteur $tuteur)
    {
        $this->_em->persist($tuteur);
    }
}
