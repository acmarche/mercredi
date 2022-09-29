<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaineGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineGroupe[]    findAll()
 * @method PlaineGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PlaineGroupeRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, PlaineGroupe::class);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('plaine_groupe')
            ->orderBy('plaine_groupe.nom', 'ASC');
    }

    public function findOneByPlaineAndGroupe(Plaine $plaine, GroupeScolaire $groupeScolaire): ?PlaineGroupe
    {
        return $this->createQueryBuilder('plaine_groupe')
            ->andWhere('plaine_groupe.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->andWhere('plaine_groupe.groupe_scolaire = :groupe')
            ->setParameter('groupe', $groupeScolaire)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Plaine $plaine
     * @return array|PlaineGroupe[]
     */
    public function findByPlaine(Plaine $plaine): array
    {
        return $this->createQueryBuilder('plaine_groupe')
            ->leftJoin('plaine_groupe.groupe_scolaire', 'groupeScolaire', 'WITH')
            ->addSelect('groupeScolaire')
            ->andWhere('plaine_groupe.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->orderBy('groupeScolaire.ordre')
            ->getQuery()
            ->getResult();
    }
}
