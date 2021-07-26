<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Plaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plaine[]    findAll()
 * @method Plaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PlaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Plaine::class);
    }

    public function findPlaineOpen(Plaine $plaine): ?Plaine
    {
        return $this->createQueryBuilder('plaine')
            ->andWhere('plaine.inscriptionOpen = 1')
            ->andWhere('plaine != :plaine')
            ->setParameter('plaine', $plaine)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('plaine')
            ->orderBy('plaine.nom', 'ASC');
    }

    public function remove(Plaine $plaine): void
    {
        $this->_em->remove($plaine);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Plaine $plaine): void
    {
        $this->_em->persist($plaine);
    }
}
