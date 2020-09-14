<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineJour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaineJour|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineJour|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineJour[]    findAll()
 * @method PlaineJour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PlaineJourRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const JOUR = 'jour';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, PlaineJour::class);
    }

    /**
     * @return PlaineJour[]
     */
    public function findByPlaine(Plaine $plaine): array
    {
        return $this->createQueryBuilder('plaine_jour')
            ->leftJoin('plaine_jour.jour', self::JOUR, 'WITH')
            ->addSelect(self::JOUR)
            ->andWhere('plaine_jour.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->addOrderBy('jour.date_jour', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPlaineAndJour(Plaine $plaine, ?Jour $jour): ?PlaineJour
    {
        return $this->createQueryBuilder('plaine_jour')
            ->andWhere('plaine_jour.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->andWhere('plaine_jour.jour = :jour')
            ->setParameter(self::JOUR, $jour)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(PlaineJour $plaineJour): void
    {
        $this->_em->remove($plaineJour);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(PlaineJour $plaineJour): void
    {
        $this->_em->persist($plaineJour);
    }
}
