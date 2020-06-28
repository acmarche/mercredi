<?php

namespace AcMarche\Mercredi\Enfant\Repository;

use AcMarche\Mercredi\Entity\Ecole;
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
     *
     * @return Enfant[]
     */
    public function findByName(string $keyword, bool $actif = true): array
    {
        $qb = $this->createQueryBuilder('enfant')
            ->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%');

        if ($actif) {
            $qb->andWhere('enfant.archived = 0');
        }

        return $qb->addOrderBy('enfant.nom', 'ASC')
            ->getQuery()->getResult();
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

    /**
     * @return Enfant[]
     */
    public function search(?string $nom, ?Ecole $ecole, ?string $annee_scolaire, bool $archive = false): array
    {
        $qb = $this->createQueryBuilder('enfant')
            ->leftJoin('enfant.ecole', 'ecole', 'WITH')
            ->leftJoin('enfant.relations', 'relations', 'WITH')
            ->addSelect('ecole', 'relations');

        if ($nom) {
            $qb->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
                ->setParameter('keyword', '%'.$nom.'%');
        }

        if ($ecole) {
            $qb->andWhere('ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($annee_scolaire) {
            $qb->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $annee_scolaire);
        }

        if ($archive) {
            $qb->andWhere('enfant.archived = 1');
        } else {
            $qb->andWhere('enfant.archived = 0');
        }

        return $qb->addOrderBy('enfant.nom', 'ASC')
            ->getQuery()->getResult();
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
