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
class GroupeScolaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupeScolaire::class);
    }

    /**
     * @return GroupeScolaire[]
     */
    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->orderBy('groupe_scolaire.nom', 'DESC')->getQuery()->getResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->orderBy('groupe_scolaire.nom', 'DESC');
    }

    public function findByAnneeScolaire(?string $annee_scolaire): ?GroupeScolaire
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->andWhere(':annee MEMBER OF groupe_scolaire.annees_scolaires')
            ->setParameter('annee', $annee_scolaire)
            ->getQuery()->getOneOrNullResult();
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
