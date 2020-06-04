<?php

namespace AcMarche\Mercredi\Sante\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SanteFiche|null   find($id, $lockMode = null, $lockVersion = null)
 * @method SanteFiche|null   findOneBy(array $criteria, array $orderBy = null)
 * @method SanteFiche[]|null findAll()
 * @method SanteFiche[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanteFicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SanteFiche::class);
    }

    public function getByEnfants(iterable $enfants)
    {
        $qb = $this->createQueryBuilder('sante_fiche');

        $qb->andWhere('sante_fiche.enfant IN (:enfants)')
            ->setParameter('enfants', $enfants);

        return $qb->getQuery()->getResult();
    }

    public function persist(SanteFiche $enfant)
    {
        $this->_em->persist($enfant);
    }

    public function remove(SanteFiche $enfant)
    {
        $this->_em->remove($enfant);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function findByEnfant(Enfant $enfant): ?SanteFiche
    {
        return $this->createQueryBuilder('sante_fiche')->andWhere('sante_fiche.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getOneOrNullResult();
    }
}
