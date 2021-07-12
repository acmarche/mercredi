<?php

namespace AcMarche\Mercredi\Accueil\Repository;

use DateTime;
use DateTimeInterface;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Accueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accueil[]    findAll()
 * @method Accueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AccueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Accueil::class);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('accueil')
            ->orderBy('accueil.date_jour', 'ASC');
    }

    public function isRegistered(Accueil $accueil, Enfant $enfant): ?Accueil
    {
        return $this->createQueryBuilder('accueil')
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $accueil->getDateJour()->format('Y-m-d'))
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $accueil->getHeure())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder('accueil')
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByEnfantAndHeure(Enfant $enfant, string $heure): array
    {
        return $this->createQueryBuilder('accueil')
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $heure)
            ->getQuery()->getResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('accueil')
            ->andWhere('accueil.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @param DateTime $date
     * @param string|null $heure
     * @param array $ecoles
     * @return Accueil[]
     */
    public function findByDateAndHeureAndEcoles(DateTimeInterface $date, ?string $heure, iterable $ecoles): array
    {
        $qb = $this->createQueryBuilder('accueil')
            ->leftJoin('accueil.enfant', 'enfant', 'WITH')
            ->leftJoin('enfant.ecole', 'ecole', 'WITH')
            ->addSelect('enfant', 'ecole')
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $date);

        if ($heure) {
            $qb->andWhere('accueil.heure = :heure')
                ->setParameter('heure', $heure)
                ->getQuery()->getResult();
        }

        if (count($ecoles) > 0) {
            $qb->andWhere('ecole IN (:ecoles)')
                ->setParameter('ecoles', $ecoles)
                ->getQuery()->getResult();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param DateTime $date
     * @param string|null $heure
     * @param array $ecoles
     * @return Accueil[]
     */
    public function findByDateAndHeure(DateTimeInterface $date, ?string $heure): array
    {
        $qb = $this->createQueryBuilder('accueil')
            ->leftJoin('accueil.enfant', 'enfant', 'WITH')
            ->leftJoin('enfant.ecole', 'ecole', 'WITH')
            ->addSelect('enfant', 'ecole')
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $date);

        if ($heure) {
            $qb->andWhere('accueil.heure = :heure')
                ->setParameter('heure', $heure)
                ->getQuery()->getResult();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Tuteur $tuteur
     * @param \DateTime|null $date
     * @return Accueil[]
     */
    public function getAccueilsNonPayesByTuteurAndMonth(Tuteur $tuteur, ?DateTime $date = null): array
    {
        $qb = $this->createQueryBuilder('accueil')
            ->leftJoin('accueil.tuteur', 'tuteur', 'WITH')
            ->leftJoin('accueil.enfant', 'enfant', 'WITH')
            ->leftJoin('accueil.facture_accueils', 'facture_accueils', 'WITH')
            ->addSelect('tuteur', 'enfant', 'facture_accueils')
            ->andWhere('accueil.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andWhere('facture_accueils IS NULL');

        if ($date) {
            $qb->andWhere('accueil.date_jour LIKE :date')
                ->setParameter('date', $date->format('Y-m').'%');
        }

        return $qb->getQuery()->getResult();
    }

    public function remove(Accueil $accueil): void
    {
        $this->_em->remove($accueil);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Accueil $accueil): void
    {
        $this->_em->persist($accueil);
    }

}
