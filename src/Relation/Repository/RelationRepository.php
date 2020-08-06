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
final class RelationRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const RELATION = 'relation';
    /**
     * @var string
     */
    private const TUTEUR = 'tuteur';
    /**
     * @var string
     */
    private const WITH = 'WITH';
    /**
     * @var string
     */
    private const ENFANT = 'enfant';
    /**
     * @var string
     */
    private const ASC = 'ASC';
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Relation::class);
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByEnfant(Enfant $enfant)
    {
        return $this->repository->createQueryBuilder(self::RELATION)
            ->leftJoin('relation.tuteur', self::TUTEUR, self::WITH)
            ->addSelect(self::TUTEUR)
            ->andWhere('relation.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->orderBy('tuteur.nom', self::ASC)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByTuteur(Tuteur $tuteur)
    {
        return $this->repository->createQueryBuilder(self::RELATION)
            ->leftJoin('relation.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andWhere('relation.tuteur = :tuteur')
            ->setParameter(self::TUTEUR, $tuteur)
            ->orderBy('enfant.prenom', self::ASC)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Relation[] Returns an array of Relation objects
     */
    public function findByEcole(Ecole $ecole)
    {
        return $this->repository->createQueryBuilder(self::RELATION)
            ->leftJoin('relation.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andWhere('enfant.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->orderBy('enfant.prenom', self::ASC)
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

    public function findOneByTuteurAndEnfant(Tuteur $tuteur, Enfant $enfant): ?Relation
    {
        return $this->repository->findOneBy([self::TUTEUR => $tuteur, self::ENFANT => $enfant]);
    }

    /**
     * @return Enfant[]
     */
    public function findFrateries(Enfant $enfant, array $tuteurs = [])
    {
        $enfants = [];
        if (0 === \count($tuteurs)) {
            $tuteurs = $this->findTuteursByEnfant($enfant);
        }

        $relations = $this->repository->createQueryBuilder(self::RELATION)
            ->leftJoin('relation.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andWhere('relation.tuteur IN (:tuteurs)')
            ->setParameter('tuteurs', $tuteurs)
            ->andWhere('relation.enfant != :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->orderBy('enfant.prenom', self::ASC)
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
        return $this->repository->createQueryBuilder(self::RELATION)
            ->leftJoin('relation.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andwhere('relation.tuteur = :tuteur')
            ->setParameter(self::TUTEUR, $tuteur)
            ->andwhere('enfant.archived != 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Relation[]
     */
    public function findTuteursActifs(): array
    {
        return $this->repository->createQueryBuilder(self::RELATION)
            ->leftJoin('relation.enfant', self::ENFANT, self::WITH)
            ->leftJoin('relation.tuteur', self::TUTEUR, self::WITH)
            ->addSelect(self::ENFANT, self::TUTEUR)
            ->andwhere('enfant.archived != 1')
            ->getQuery()
            ->getResult();
    }

    public function remove(Relation $relation): void
    {
        $this->_em->remove($relation);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Relation $relation): void
    {
        $this->_em->persist($relation);
    }
}
