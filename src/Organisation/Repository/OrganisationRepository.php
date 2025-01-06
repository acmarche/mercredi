<?php

namespace AcMarche\Mercredi\Organisation\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Organisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organisation[]    findAll()
 * @method Organisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class OrganisationRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Organisation::class);
    }

    public function getOrganisation(): ?Organisation
    {
        return $this
            ->createQueryBuilder('organisation')
            ->getQuery()->getOneOrNullResult();
    }
}
