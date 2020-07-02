<?php

namespace AcMarche\Mercredi\Enfant\Repository;

use AcMarche\Mercredi\Entity\AnneeScolaire;
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
     * @return Enfant[]
     */
    public function findAllActif(): array
    {
        return $this->createQueryBuilder('enfant')
            ->andWhere('enfant.archived = 0')
            ->addOrderBy('enfant.nom', 'ASC')
            ->getQuery()->getResult();
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
    public function search(?string $nom, ?Ecole $ecole, ?AnneeScolaire $annee_scolaire, bool $archive = false): array
    {
        $qb = $this->createQueryBuilder('enfant')
            ->leftJoin('enfant.ecole', 'ecole', 'WITH')
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', 'WITH')
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', 'WITH')
            ->leftJoin('enfant.relations', 'relations', 'WITH')
            ->addSelect('ecole', 'relations', 'sante_fiche', 'annee_scolaire');

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
