<?php

namespace AcMarche\Mercredi\Organisation\Repository;

use AcMarche\Mercredi\Entity\Organisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organisation[]    findAll()
 * @method Organisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organisation::class);
    }

    public function getOrganisation(): ?Organisation
    {
        return  $this->createQueryBuilder('organisation')
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(Organisation $organisation)
    {
        $this->_em->remove($organisation);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Organisation $organisation)
    {
        $this->_em->persist($organisation);
    }
}
