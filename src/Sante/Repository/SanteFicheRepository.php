<?php

namespace AcMarche\Mercredi\Sante\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SanteFiche|null   find($id, $lockMode = null, $lockVersion = null)
 * @method SanteFiche|null   findOneBy(array $criteria, array $orderBy = null)
 * @method SanteFiche[]|null findAll()
 * @method SanteFiche[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SanteFicheRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SanteFiche::class);
    }

    public function getByEnfants(iterable $enfants)
    {
        $queryBuilder = $this->createQueryBuilder('sante_fiche');

        $queryBuilder->andWhere('sante_fiche.enfant IN (:enfants)')
            ->setParameter('enfants', $enfants);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByEnfant(Enfant $enfant): ?SanteFiche
    {
        return $this->createQueryBuilder('sante_fiche')->andWhere('sante_fiche.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getOneOrNullResult();
    }
}
