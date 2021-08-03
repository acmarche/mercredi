<?php

namespace AcMarche\Mercredi\Relation\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relation[]    findAll()
 * @method Relation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class RelationRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Relation::class);
    }

    private function getQlB(): QueryBuilder
    {
        return $this->createQueryBuilder('relation')
            ->leftJoin('relation.tuteur', 'tuteur', 'WITH')
            ->leftJoin('relation.enfant', 'enfant', 'WITH')
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', 'WITH')
            ->leftJoin('tuteur.users', 'users', 'WITH')
            ->addSelect('tuteur', 'enfant', 'sante_fiche', 'users');
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByEnfant(Enfant $enfant)
    {
        return $this->getQlB()
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
        return $this->getQlB()
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
        return $this->getQlB()
            ->andWhere('enfant.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->orderBy('enfant.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tuteur[] Returns an array of RelationEnfant objects
     */
    public function findTuteursByEnfant(Enfant $enfant): array
    {
        $tuteurs = [];
        $relations = $this->findByEnfant($enfant);

        foreach ($relations as $relation) {
            $tuteurs[] = $relation->getTuteur();
        }

        return $tuteurs;
    }


    public function findOneByTuteurAndEnfant(Tuteur $tuteur, Enfant $enfant): ?Relation
    {
        return $this->findOneBy(['tuteur' => $tuteur, 'enfant' => $enfant]);
    }

    /**
     * @return Enfant[]
     */
    public function findFrateries(Enfant $enfant, array $tuteurs = []): array
    {
        $enfants = [];
        if (0 === \count($tuteurs)) {
            $tuteurs = $this->findTuteursByEnfant($enfant);
        }

        $relations = $this->getQlB()
            ->andWhere('relation.tuteur IN (:tuteurs)')
            ->setParameter('tuteurs', $tuteurs)
            ->andWhere('relation.enfant != :enfant')
            ->setParameter('enfant', $enfant)
            ->orderBy('enfant.prenom', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($relations as $relation) {
            $enfants[] = $relation->getEnfant();
        }

        return $enfants;
    }

    /**
     * @return Relation[]
     */
    public function findEnfantsActifs(Tuteur $tuteur): array
    {
        return $this->getQlB()
            ->andwhere('relation.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andwhere('enfant.archived != 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Relation[]
     */
    public function findTuteursActifs(): array
    {
        return $this->getQlB()
            ->andwhere('enfant.archived != 1')
            ->getQuery()
            ->getResult();
    }

    public function findByEnfants(array $enfants)
    {
        return $this->getQlB()
            ->andwhere('relation.enfant IN (:enfants)')
            ->setParameter('enfants', $enfants)
            ->getQuery()
            ->getResult();
    }
}
