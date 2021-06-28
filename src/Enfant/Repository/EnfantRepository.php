<?php

namespace AcMarche\Mercredi\Enfant\Repository;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Enfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfant[]    findAll()
 * @method Enfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnfantRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const ECOLE = 'ecole';
    /**
     * @var string
     */
    private const WITH = 'WITH';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Enfant::class);
    }

    /**
     * @return Enfant[]
     */
    public function findAllActif(): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder();
        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $keyword
     *
     * @return Enfant[]
     */
    public function findByName(string $keyword, bool $actif = true): array
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%');

        if ($actif) {
            $queryBuilder->andWhere('enfant.archived = 0');
        }

        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $ecoles
     * @return Enfant[]
     */
    public function findByEcoles(iterable $ecoles): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.relations', 'relations', self::WITH)
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', self::WITH)
            ->addSelect('relations', 'annee_scolaire')
            ->andWhere('enfant.ecole IN (:ecoles)')
            ->setParameter('ecoles', $ecoles);

        return $this->addOrderByNameQueryBuilder($queryBuilder)
            ->getQuery()->getResult();
    }

    /**
     * @param array $ecoles
     * @return Enfant[]
     */
    public function findByEcolesForEcole(iterable $ecoles): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.relations', 'relations', self::WITH)
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', self::WITH)
            ->addSelect('relations', 'annee_scolaire')
            ->andWhere('enfant.ecole IN (:ecoles)')
            ->setParameter('ecoles', $ecoles);

        $queryBuilder->andWhere('enfant.accueil_ecole = 1');

        return $this->addOrderByNameQueryBuilder($queryBuilder)
            ->getQuery()->getResult();
    }

    /**
     * J'exclus les enfants sans tuteur !
     * @param Ecole $ecole
     * @return Enfant[]
     */
    public function findByEcolesForInscription(Ecole $ecole): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.relations', 'relations', self::WITH)
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', self::WITH)
            ->addSelect('relations', 'annee_scolaire')
            ->andWhere('enfant.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->andWhere('relations IS NOT NULL');

        $queryBuilder->andWhere('enfant.accueil_ecole = 1');

        return $this->addOrderByNameQueryBuilder($queryBuilder)
            ->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function findOrphelins()
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->andWhere('enfant.relations IS EMPTY');

        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function search(?string $nom, ?Ecole $ecole, ?AnneeScolaire $anneeScolaire): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.ecole', self::ECOLE, self::WITH)
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', self::WITH)
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', self::WITH)
            ->leftJoin('enfant.relations', 'relations', self::WITH)
            ->addSelect(self::ECOLE, 'relations', 'sante_fiche', 'annee_scolaire');

        if ($nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
                ->setParameter('keyword', '%'.$nom.'%');
        }

        if (null !== $ecole) {
            $queryBuilder->andWhere('ecole = :ecole')
                ->setParameter(self::ECOLE, $ecole);
        }

        if ($anneeScolaire !== null) {
            $queryBuilder->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $anneeScolaire);
        }

        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }


    public function searchForEcole(iterable $ecoles, ?string $nom, bool $strict = true)
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.ecole', self::ECOLE, self::WITH)
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', self::WITH)
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', self::WITH)
            ->leftJoin('enfant.relations', 'relations', self::WITH)
            ->addSelect(self::ECOLE, 'relations', 'sante_fiche', 'annee_scolaire');

        if ($nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
                ->setParameter('keyword', '%'.$nom.'%');
        }

        $queryBuilder->andWhere('enfant.ecole IN (:ecoles)')
            ->setParameter('ecoles', $ecoles);

        if ($strict) {
            $queryBuilder->andWhere('enfant.accueil_ecole = 1');
        }

        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Animateur $animateur
     * @return array|Enfant[]
     */
    public function findAllForAnimateur(Animateur $animateur): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.presences', 'presences', self::WITH)
            ->addSelect('presences');

        $jours = $this->getEntityManager()->getRepository(Jour::class)->findByAnimateur($animateur);

        if (count($jours) == 0) {
            return [];
        }

        $presences = $this->getEntityManager()->getRepository(Presence::class)->findPresencesByJours($jours);

        $queryBuilder->andWhere('presences IN (:presences)')
            ->setParameter('presences', $presences);

        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Animateur $animateur
     * @param string|null $nom
     * @param Jour|null $jour
     * @return array|Enfant[]
     */
    public function searchForAnimateur(Animateur $animateur, ?string $nom, ?Jour $jour): array
    {
        $queryBuilder = $this->addNotArchivedQueryBuilder()
            ->leftJoin('enfant.presences', 'presences', self::WITH)
            ->addSelect('presences');

        if ($jour !== null) {
            $jours = [$jour];
        } else {
            $jours = $this->getEntityManager()->getRepository(Jour::class)->findByAnimateur($animateur);
        }
        if (count($jours) == 0) {
            return [];
        }

        $presences = $this->getEntityManager()->getRepository(Presence::class)->findPresencesByJours($jours);

        if ($nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
                ->setParameter('keyword', '%'.$nom.'%');
        }

        $queryBuilder->andWhere('presences IN (:presences)')
            ->setParameter('presences', $presences);

        $this->addOrderByNameQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    private function getOrCreateQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        if ($qb !== null) {
            return $qb;
        }

        return $this->createQueryBuilder('enfant');
    }

    private function addOrderByNameQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->addOrderBy('enfant.nom', 'ASC');
    }

    private function addNotArchivedQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('enfant.archived = 0');
    }

    public function remove(Enfant $enfant): void
    {
        $this->_em->remove($enfant);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Enfant $enfant): void
    {
        $this->_em->persist($enfant);
    }
}
