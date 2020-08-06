<?php

namespace AcMarche\Mercredi\Presence\Repository;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Utils\PlaineUtils;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PresenceRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const PRESENCE = 'presence';
    /**
     * @var string
     */
    private const JOUR = 'jour';
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
    private const TUTEUR = 'tuteur';
    /**
     * @var string
     */
    private const JOURS = 'jours';
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(ManagerRegistry $managerRegistry, JourRepository $jourRepository)
    {
        parent::__construct($managerRegistry, Presence::class);
        $this->jourRepository = $jourRepository;
    }

    /**
     * @param Enfant $enfant
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
        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.jour', self::JOUR, self::WITH)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT, self::JOUR)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter(self::JOUR, $jour)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Enfant $enfant
     * @return Presence[]
     */
    public function findPresencesByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.tuteur', self::TUTEUR, self::WITH)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->addSelect(self::TUTEUR)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @param Tuteur $tuteur
     * @return Presence[]
     */
    public function findPresencesByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.tuteur', self::TUTEUR, self::WITH)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT, self::TUTEUR)
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter(self::TUTEUR, $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @param Plaine $plaine
     * @return Presence[]
     */
    public function findPresencesByPlaine(Plaine $plaine): array
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);

        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->leftJoin('presence.jour', self::JOUR, self::WITH)
            ->addSelect(self::ENFANT, self::JOUR)
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter(self::JOURS, $jours)
            ->getQuery()->getResult();
    }

    /**
     * @param Plaine $plaine
     * @param Tuteur $tuteur
     * @return Presence[]
     */
    public function findPresencesByPlaineAndTuteur(Plaine $plaine, Tuteur $tuteur): array
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);

        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->leftJoin('presence.jour', self::JOUR, self::WITH)
            ->addSelect(self::ENFANT, self::JOUR)
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter(self::JOURS, $jours)
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter(self::TUTEUR, $tuteur)
            ->getQuery()->getResult();
    }

    public function findPresencesByPlaineAndEnfant(Plaine $plaine, Enfant $enfant)
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);

        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->leftJoin('presence.jour', self::JOUR, self::WITH)
            ->addSelect(self::ENFANT, self::JOUR)
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter(self::JOURS, $jours)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @param Enfant $enfant
     * @return Presence[]
     */
    public function findPlainesByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder(self::PRESENCE)
            ->leftJoin('presence.enfant', self::ENFANT, self::WITH)
            ->leftJoin('presence.jour', self::JOUR, self::WITH)
            ->leftJoin('jour.plaine_jour', 'plaine_jour', self::WITH)
            ->addSelect(self::ENFANT, self::JOUR, 'plaine_jour')
            ->andWhere('plaine_jour IS NOT NULL')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @param Enfant $enfant
     * @param Jour $jour
     * @return Presence
     *
     * @throws NonUniqueResultException
     */
    public function isRegistered(Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQueryBuilder(self::PRESENCE)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter(self::JOUR, $jour)
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

        return $this->createQueryBuilder(self::PRESENCE)
            ->join('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter(self::JOURS, $jours)
            ->getQuery()->getResult();
    }

    /**
     * @param $jour
     *
     * @return Presence[]
     */
    public function findByDay($jour)
    {
        return $this->createQueryBuilder(self::PRESENCE)
            ->join('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andWhere('presence.jour = :jour')
            ->setParameter(self::JOUR, $jour)
            ->getQuery()->getResult();
    }

    public function findOneByEnfantJour(Enfant $enfant, $jour): ?Presence
    {
        return $this->createQueryBuilder(self::PRESENCE)
            ->join('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT)
            ->andWhere('presence.jour = :jour')
            ->setParameter(self::JOUR, $jour)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter(self::ENFANT, $enfant)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Jour $jour
     * @param Ecole|null $ecole
     *
     * @return Presence[]
     */
    public function findPresencesByJourAndEcole(Jour $jour, ?Ecole $ecole): array
    {
        $queryBuilder = $this->createQueryBuilder(self::PRESENCE)
            ->join('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT);

        if ($jour) {
            $queryBuilder->andWhere('presence.jour = :jour')
                ->setParameter(self::JOUR, $jour);
        }

        if ($ecole !== null) {
            $queryBuilder->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $nom
     * @param Ecole $ecole
     * @param string $annee_scolaire
     * @return Presence[]
     */
    public function search(string $nom, Ecole $ecole, string $annee_scolaire): array
    {
        $queryBuilder = $this->createQueryBuilder(self::PRESENCE)
            ->join('presence.enfant', self::ENFANT, self::WITH)
            ->addSelect(self::ENFANT);

        if ($nom !== '') {
            $queryBuilder->andWhere('enfant.nom LIKE :nom')
                ->setParameter('nom', $nom);
        }

        if ($ecole) {
            $queryBuilder->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($annee_scolaire !== '') {
            $queryBuilder->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $annee_scolaire);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function remove(Presence $presence): void
    {
        $this->_em->remove($presence);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Presence $presence): void
    {
        $this->_em->persist($presence);
    }
}
