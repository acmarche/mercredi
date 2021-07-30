<?php

namespace AcMarche\Mercredi\Presence\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Utils\PlaineUtils;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PresenceRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    private JourRepository $jourRepository;

    public function __construct(ManagerRegistry $managerRegistry, JourRepository $jourRepository)
    {
        parent::__construct($managerRegistry, Presence::class);
        $this->jourRepository = $jourRepository;
    }

    /**
     * @return Jour[]
     */
    public function findDaysRegisteredByEnfant(Enfant $enfant): array
    {
        $presences = $this->findPresencesByEnfant($enfant);
        $jours = [];
        foreach ($presences as $presence) {
            $jours[] = $presence->getJour();
        }

        return $jours;
    }

    public function findPresencesByEnfantAndJour(Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant', 'jour')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.tuteur', 'tuteur', 'WITH')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->addSelect('tuteur')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.tuteur', 'tuteur', 'WITH')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant', 'tuteur')
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByTuteurAndMonth(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $qb = $this->createQueryBuilder('presence')
            ->leftJoin('presence.tuteur', 'tuteur', 'WITH')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->addSelect('enfant', 'tuteur', 'jour')
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur);

        if ($date) {
            $qb->andWhere('jour.date_jour LIKE :date')
                ->setParameter('date', $date->format('Y-m').'%');
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * @return Presence[]
     */
    public function findPresencesByPlaine(Plaine $plaine): array
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);

        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->addSelect('enfant', 'jour')
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByPlaineAndTuteur(Plaine $plaine, Tuteur $tuteur): array
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);

        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->addSelect('enfant', 'jour')
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByPlaineAndEnfant(Plaine $plaine, Enfant $enfant): array
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);

        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->addSelect('enfant', 'jour')
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findPlainesByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->leftJoin('jour.plaine_jour', 'plaine_jour', 'WITH')
            ->addSelect('enfant', 'jour', 'plaine_jour')
            ->andWhere('plaine_jour IS NOT NULL')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence
     *
     * @throws NonUniqueResultException
     */
    public function isRegistered(Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQueryBuilder('presence')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param DateTimeInterface $dateTime mm/YYYY
     *
     * @return Presence[]
     */
    public function findByMonth(DateTimeInterface $dateTime): array
    {
        $jours = $this->jourRepository->findDaysByMonth($dateTime);

        return $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->getQuery()->getResult();
    }

    /**
     * @param $jour
     *
     * @return Presence[]
     */
    public function findByDay($jour)
    {
        return $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getResult();
    }

    public function findOneByEnfantJour(Enfant $enfant, $jour): ?Presence
    {
        return $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByJourAndEcole(Jour $jour, ?Ecole $ecole): array
    {
        $queryBuilder = $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant');

        if ($jour) {
            $queryBuilder->andWhere('presence.jour = :jour')
                ->setParameter('jour', $jour);
        }

        if (null !== $ecole) {
            $queryBuilder->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array|Jour[] $jours
     * @return Presence[]
     */
    public function findPresencesByJours(array $jours): array
    {
        $queryBuilder = $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->join('presence.jour', 'jour', 'WITH')
            ->addSelect('enfant', 'jour');

        $queryBuilder->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function search(string $nom, Ecole $ecole, string $annee_scolaire): array
    {
        $queryBuilder = $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant');

        if ('' !== $nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :nom')
                ->setParameter('nom', $nom);
        }

        if ($ecole) {
            $queryBuilder->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ('' !== $annee_scolaire) {
            $queryBuilder->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $annee_scolaire);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
