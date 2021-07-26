<?php

namespace AcMarche\Mercredi\Scolaire\Repository;

use AcMarche\Mercredi\Entity\GroupeScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupeScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeScolaire[]    findAll()
 * @method GroupeScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GroupeScolaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, GroupeScolaire::class);
    }

    /**
     * @return GroupeScolaire[]
     */
    public function findAllForPlaineOrderByNom(): array
    {
        return $this->getQbForListingPlaine()
            ->orderBy('groupe_scolaire.nom', 'DESC')->getQuery()->getResult();
    }

    public function getQbForListingPresence(): QueryBuilder
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->andWhere('groupe_scolaire.is_plaine != 1')
            ->orderBy('groupe_scolaire.nom', 'DESC');
    }

    public function getQbForListingPlaine()
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->andWhere('groupe_scolaire.is_plaine = 1')
            ->orderBy('groupe_scolaire.nom', 'DESC');
    }

    public function remove(GroupeScolaire $groupeScolaire): void
    {
        $this->_em->remove($groupeScolaire);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(GroupeScolaire $groupeScolaire): void
    {
        $this->_em->persist($groupeScolaire);
    }

}
