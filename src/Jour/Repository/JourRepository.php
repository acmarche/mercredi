<?php

namespace AcMarche\Mercredi\Jour\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jour[]    findAll()
 * @method Jour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jour::class);
    }

    /**
     * @param $enfant
     */
    public function getQbDaysNotRegisteredByEnfant($enfant): QueryBuilder
    {
        $joursRegistered = $this->getEntityManager()->getRepository(Presence::class)
            ->findDaysRegisteredByEnfant($enfant);

        $qb = $this->createQueryBuilder('jour');
        if (count($joursRegistered) > 0) {
            $qb
                ->andWhere('jour.id NOT IN (:jours)')
                ->setParameter('jours', $joursRegistered);
        }
        $qb->andwhere('jour.archived = 0')
            ->orderBy('jour.date_jour', 'DESC');

        $this->filterPlaine($qb);

        return $qb;
    }

    /**
     * @param string $date
     *
     * @return Jour[]
     */
    public function findDaysByMonth(\DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('jour')
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter('date', $date->format('Y-m').'%')
            ->addOrderBy('jour.date_jour', 'ASC');

        $this->filterPlaine($qb);

        return $qb->getQuery()->getResult();
    }

    public function findActifs()
    {
        $qb = $this->createQueryBuilder('jour')
            ->andWhere('jour.archived = 0')
            ->orderBy('jour.date_jour', 'DESC');

        $this->filterPlaine($qb);

        return $qb->getQuery()->getResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('jour')
            ->andWhere('jour.archived = 0')
            ->orderBy('jour.date_jour', 'DESC');

        $this->filterPlaine($qb);

        return $qb;
    }

    public function getQbForParent(Enfant $enfant): QueryBuilder
    {
        $qb = $this->getQbDaysNotRegisteredByEnfant($enfant);

        /**
         * je ne propose pas les dates passees.
         */
        $date_time = new \DateTime();
        $today = $date_time->format('Y-m-d');

        $qb->andWhere('jour.date_jour >= :today')
            ->setParameter('today', $today);

        return $qb;
    }

    private function filterPlaine(QueryBuilder $queryBuilder)
    {
        $queryBuilder->andWhere('jour.plaine IS NULL');
    }

    public function remove(Jour $jour)
    {
        $this->_em->remove($jour);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Jour $jour)
    {
        $this->_em->persist($jour);
    }
}
