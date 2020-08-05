<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Plaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plaine[]    findAll()
 * @method Plaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plaine::class);
    }

    public function findPlaineOpen(): ?Plaine
    {
        return $this->createQueryBuilder('plaine')
            ->andWhere('plaine.inscriptionOpen = 1')
            ->getQuery()
            ->getOneOrNullResult();
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

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('plaine')
            ->orderBy('plaine.nom', 'ASC');
    }
}
