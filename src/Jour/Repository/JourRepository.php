<?php

namespace AcMarche\Mercredi\Jour\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
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
    use OrmCrudTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private PresenceRepository $presenceRepository
    ) {
        parent::__construct($managerRegistry, Jour::class);
    }

    public function getQlNotPlaine(bool $archive = false): QueryBuilder
    {
        return $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->andwhere('jour.archived = :archive')
            ->setParameter('archive', $archive)
            ->andWhere('jour.plaine IS NULL')
            ->addOrderBy('jour.date_jour', 'DESC');
    }

    public function getQbDaysNotRegisteredByEnfant(Enfant $enfant): QueryBuilder
    {
        $joursRegistered = $this->presenceRepository->findDaysRegisteredByEnfant(
            $enfant
        );

        $queryBuilder = $this->getQlNotPlaine();

        if ([] !== $joursRegistered) {
            $queryBuilder
                ->andWhere('jour.id NOT IN (:jours)')
                ->setParameter('jours', $joursRegistered);
        }

        return $queryBuilder;
    }

    /**
     * @param ?bool $filter null => only mercredi, true only plaine, false both
     * @return Jour[]
     * @see PresenceUtils
     */
    public function findDaysByMonth(DateTimeInterface $dateTime, ?bool $filter = null): array
    {
        $qb = $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter('date', $dateTime->format('Y-m').'%')
            ->addOrderBy('jour.date_jour', 'ASC');

        if ($filter === true) {
            $qb->andWhere('plaine IS NOT NULL');
        }

        if ($filter === null) {
            $qb->andWhere('plaine IS NULL');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function search(bool $archive, ?bool $pedagogique): array
    {
        $qb = $this->getQlNotPlaine($archive);
        switch ($pedagogique) {
            case true | false:
                $qb->andwhere('jour.pedagogique = :pedagogique')
                    ->setParameter('pedagogique', $pedagogique);
                break;
            default:
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function findPedagogiqueByDateGreatherOrEqualAndNotRegister(
        DateTimeInterface $dateTime,
        Enfant $enfant
    ): array {
        return $this->getQbDaysNotRegisteredByEnfant($enfant)
            ->andWhere('jour.date_jour >= :date')
            ->setParameter('date', $dateTime->format('Y-m-d').'%')
            ->andWhere('jour.pedagogique = 1')
            ->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function findJourNotPedagogiqueByDateGreatherOrEqualAndNotRegister(
        DateTimeInterface $dateTime,
        Enfant $enfant
    ): array {
        return $this->getQbDaysNotRegisteredByEnfant($enfant)
            ->andWhere('jour.date_jour >= :date')
            ->setParameter('date', $dateTime->format('Y-m-d').'%')
            ->andWhere('jour.pedagogique = 0')
            ->getQuery()->getResult();
    }

    public function getQlJourByDateGreatherOrEqualAndNotRegister(
        Enfant $enfant,
        DateTimeInterface $dateTime
    ): QueryBuilder {
        return $this->getQbDaysNotRegisteredByEnfant($enfant)
            ->andWhere('jour.date_jour >= :date')
            ->setParameter('date', $dateTime->format('Y-m-d').'%');
    }

    /**
     * @param DateTimeInterface $dateSelected
     * @return Jour[]
     */
    public function findByDate(\DateTimeInterface $dateSelected): array
    {
        return $this->createQueryBuilder('jour')
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter('date', $dateSelected->format('Y-m-d').'%')
            ->getQuery()->getResult();
    }

    /**
     * @return array|Jour[]
     */
    public function findByAnimateur(Animateur $animateur): array
    {
        return $this->getQlNotPlaine()
            ->andWhere(':animateur MEMBER OF jour.animateurs')
            ->setParameter('animateur', $animateur)
            ->getQuery()->getResult();
    }

    /**
     * @return array|Jour[]
     */
    public function findByPlaine(Plaine $plaine): array
    {
        return $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->setParameter('plaine', $plaine)
            ->andWhere('jour.plaine = :plaine')
            ->addOrderBy('jour.date_jour', 'ASC')
            ->getQuery()->getResult();
    }

    public function findByPlaineQb(Plaine $plaine): QueryBuilder
    {
        return $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->setParameter('plaine', $plaine)
            ->andWhere('jour.plaine = :plaine')
            ->addOrderBy('jour.date_jour', 'ASC');

    }

}
