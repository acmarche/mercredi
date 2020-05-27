<?php

namespace AcMarche\Mercredi\Relation\Repository;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relation[]    findAll()
 * @method Relation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relation::class);
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByEnfant(Enfant $enfant)
    {
        return $this->createQueryBuilder('relation')
            ->leftJoin('relation.tuteur', 'tuteur', 'WITH')
            ->addSelect('tuteur')
            ->andWhere('relation.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->orderBy('tuteur.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByTuteur(Tuteur $tuteur)
    {
        return $this->createQueryBuilder('relation')
            ->leftJoin('relation.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('relation.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->orderBy('enfant.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByEcole(Ecole $ecole)
    {
        return $this->createQueryBuilder('relation')
            ->leftJoin('relation.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('enfant.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->orderBy('enfant.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tuteur[] Returns an array of RelationEnfant objects
     */
    public function findTuteursByEnfant(Enfant $enfant)
    {
        $tuteurs = [];
        $relations = $this->findByEnfant($enfant);

        foreach ($relations as $relation) {
            $tuteurs[] = $relation->getTuteur();
        }

        return $tuteurs;
    }

    /**
     * @return Enfant[] Returns an array of RelationEnfant objects
     */
    public function findEnfantsByTuteur(Tuteur $tuteur)
    {
        $enfants = [];
        $relations = $this->findByTuteur($tuteur);

        foreach ($relations as $relation) {
            $enfants[] = $relation->getTuteur();
        }

        return $enfants;
    }

    public function checkExist(Tuteur $tuteur, $enfant): ?Relation
    {
        return $this->findOneBy(['tuteur' => $tuteur, 'enfant' => $enfant]);
    }

    /**
     * @param Enfant $enfant
     * @param Tuteur $tuteur
     * @return Enfant[]
     */
    public function findFrateries(Enfant $enfant, Tuteur $tuteur)
    {
        $relations = $this->createQueryBuilder('relation')
            ->leftJoin('relation.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('relation.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andWhere('relation.enfant != :enfant')
            ->setParameter('enfant', $enfant)
            ->orderBy('enfant.prenom', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($relations as $relation) {
            $enfants[] = $relation->getTuteur();
        }

        return $enfants;
    }

    public function remove(Relation $relation)
    {
        $this->_em->remove($relation);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Relation $relation)
    {
        $this->_em->persist($relation);
    }

    /**
     * @param Tuteur $tuteur
     * @return Relation[]
     */
    public function findEnfantsActifs(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('relation')
            ->leftJoin('relation.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andwhere('relation.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andwhere('enfant.archived != 1')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param Tuteur $tuteur
     * @return Relation[]
     */
    public function findTuteursActifs(): array
    {
        return $this->createQueryBuilder('relation')
            ->leftJoin('relation.enfant', 'enfant', 'WITH')
            ->leftJoin('relation.tuteur', 'tuteur', 'WITH')
            ->addSelect('enfant', 'tuteur')
            ->andwhere('enfant.archived != 1')
            ->getQuery()
            ->getResult();
    }
}
