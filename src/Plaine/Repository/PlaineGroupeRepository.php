<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method PlaineGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineGroupe[]    findAll()
 * @method PlaineGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PlaineGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, PlaineGroupe::class);
    }

    public function findPlaineOpen(): ?PlaineGroupe
    {
        return $this->createQueryBuilder('plaine')
            ->andWhere('plaine.inscriptionOpen = 1')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(PlaineGroupe $plaineGroupe): void
    {
        $this->_em->remove($plaineGroupe);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(PlaineGroupe $plaineGroupe): void
    {
        $this->_em->persist($plaineGroupe);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('plaine')
            ->orderBy('plaine.nom', 'ASC');
    }
}
