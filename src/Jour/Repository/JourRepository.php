<?php

namespace AcMarche\Mercredi\Jour\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function count;

/**
 * @method Jour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jour[]    findAll()
 * @method Jour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class JourRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const JOUR = 'jour';
    /**
     * @var string
     */
    private const PLAINE_JOUR = 'plaineJour';
    /**
     * @var string
     */
    private const WITH = 'WITH';
    /**
     * @var string
     */
    private const DESC = 'DESC';
    /**
     * @var string
     */
    private const DATE = 'date';
    /**
     * @var string
     */
    private const FORMAT = 'Y-m-d';
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(ManagerRegistry $managerRegistry, PresenceRepository $presenceRepository)
    {
        parent::__construct($managerRegistry, Jour::class);
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @param Enfant $enfant
     * @return QueryBuilder
     */
    public function getQbDaysNotRegisteredByEnfant(Enfant $enfant): QueryBuilder
    {
        $joursRegistered = $this->presenceRepository->findDaysRegisteredByEnfant($enfant);

        $queryBuilder = $this->createQueryBuilder(self::JOUR)
            ->leftJoin('jour.plaine_jour', self::PLAINE_JOUR, self::WITH)
            ->addSelect(self::PLAINE_JOUR);

        if (count($joursRegistered) > 0) {
            $queryBuilder
                ->andWhere('jour.id NOT IN (:jours)')
                ->setParameter('jours', $joursRegistered);
        }

        $queryBuilder
            ->andwhere('jour.archived = 0')
            ->andWhere('plaineJour IS NULL')
            ->orderBy('jour.date_jour', self::DESC);

        return $queryBuilder;
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return Jour[]
     */
    public function findDaysByMonth(DateTimeInterface $dateTime): array
    {
        return $this->createQueryBuilder(self::JOUR)
            ->leftJoin('jour.plaine_jour', self::PLAINE_JOUR, self::WITH)
            ->addSelect(self::PLAINE_JOUR)
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter(self::DATE, $dateTime->format('Y-m').'%')
            ->addOrderBy('jour.date_jour', 'ASC')
            ->andWhere('plaineJour IS NULL')
            ->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function findNotArchived(): array
    {
        return $this->createQueryBuilder(self::JOUR)
            ->leftJoin('jour.plaine_jour', self::PLAINE_JOUR, self::WITH)
            ->addSelect(self::PLAINE_JOUR)
            ->andWhere('jour.archived = 0')
            ->orderBy('jour.date_jour', self::DESC)
            ->andWhere('plaineJour IS NULL')
            ->getQuery()->getResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder(self::JOUR)
            ->leftJoin('jour.plaine_jour', self::PLAINE_JOUR, self::WITH)
            ->addSelect(self::PLAINE_JOUR)
            ->andWhere('jour.archived = 0')
            ->andWhere('plaineJour IS NULL')
            ->orderBy('jour.date_jour', self::DESC);
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @param Enfant $enfant
     * @return Jour[]
     */
    public function findPedagogiqueByDateGreatherOrEqual(DateTimeInterface $dateTime, Enfant $enfant): array
    {
        $queryBuilder = $this->getQbDaysNotRegisteredByEnfant($enfant);

        return $queryBuilder
            ->andWhere('jour.date_jour >= :date')
            ->setParameter(self::DATE, $dateTime->format(self::FORMAT).'%')
            ->andWhere('jour.pedagogique = 1')
            ->orderBy('jour.date_jour', self::DESC)
            ->getQuery()->getResult();
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @param Enfant $enfant
     * @return Jour[]
     */
    public function findJourByDateGreatherOrEqual(DateTimeInterface $dateTime, Enfant $enfant): array
    {
        $queryBuilder = $this->getQbDaysNotRegisteredByEnfant($enfant);

        return $queryBuilder
            ->andWhere('jour.date_jour >= :date')
            ->setParameter(self::DATE, $dateTime->format(self::FORMAT).'%')
            ->andWhere('jour.pedagogique = 0')
            ->orderBy('jour.date_jour', self::DESC)
            ->getQuery()->getResult();
    }

    public function findOneByDate(DateTime $dateTime): ?Jour
    {
        return $this->createQueryBuilder(self::JOUR)
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter(self::DATE, $dateTime->format(self::FORMAT).'%')
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(Jour $jour): void
    {
        $this->_em->remove($jour);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Jour $jour): void
    {
        $this->_em->persist($jour);
    }
}
