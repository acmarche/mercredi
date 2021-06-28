<?php

namespace AcMarche\Mercredi\Jour\Repository;

use DateTimeImmutable;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use function count;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Jour::class);
    }

    public function getQbDaysNotRegisteredByEnfant(Enfant $enfant): QueryBuilder
    {
        $joursRegistered = $this->getEntityManager()->getRepository(Presence::class)->findDaysRegisteredByEnfant(
            $enfant
        );

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
            ->orderBy('jour.date_jour', 'ASC')
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

    public function getQbForListingAnimateur(Animateur $animateur): QueryBuilder
    {
        return $this->createQueryBuilder(self::JOUR)
            ->leftJoin('jour.plaine_jour', self::PLAINE_JOUR, self::WITH)
            ->addSelect(self::PLAINE_JOUR)
            ->andWhere('jour.archived = 0')
            ->andWhere('plaineJour IS NULL')
            ->andWhere(':animateur MEMBER OF jour.animateurs')
            ->setParameter('animateur', $animateur)
            ->orderBy('jour.date_jour', self::DESC);
    }

    /**
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

    /**
     * @param DateTime|DateTimeImmutable $dateTime
     */
    public function findOneByDate(\DateTimeInterface $dateTime): ?Jour
    {
        return $this->createQueryBuilder(self::JOUR)
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter(self::DATE, $dateTime->format(self::FORMAT).'%')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Animateur $animateur
     * @return array|Jour[]
     */
    public function findByAnimateur(Animateur $animateur): array
    {
        return $this->createQueryBuilder(self::JOUR)
            ->andWhere(':animateur MEMBER OF jour.animateurs')
            ->setParameter('animateur', $animateur)
            ->andWhere('jour.archived = 0')
            ->addOrderBy('jour.date_jour', 'DESC')
            ->getQuery()->getResult();
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
