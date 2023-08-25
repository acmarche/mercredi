<?php

namespace AcMarche\Mercredi\Scolaire\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Scolaire\AnneeScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnneeScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnneeScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnneeScolaire[]    findAll()
 * @method AnneeScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AnneeScolaireRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, AnneeScolaire::class);
    }

    /**
     * @return AnneeScolaire[]
     */
    public function findAllOrderByOrdre(): array
    {
        return $this->createQueryBuilder('annee_scolaire')
            ->orderBy('annee_scolaire.ordre', Criteria::ASC)
            ->getQuery()->getResult();
    }

    public function findLast(): AnneeScolaire
    {
        return $this->createQueryBuilder('annee_scolaire')
            ->orderBy('annee_scolaire.ordre', Criteria::DESC)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function findNext(?AnneeScolaire $anneeScolaire): ?AnneeScolaire
    {
        $ordre = $anneeScolaire->getOrdre() + 1;

        return $this->createQueryBuilder('annee_scolaire')
            ->andWhere('annee_scolaire.ordre = :ordre')
            ->setParameter('ordre', $ordre)
            ->getQuery()->getOneOrNullResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('annee_scolaire')
            ->orderBy('annee_scolaire.ordre', 'ASC');
    }
}
