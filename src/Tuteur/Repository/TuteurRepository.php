<?php

namespace AcMarche\Mercredi\Tuteur\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tuteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tuteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tuteur[]    findAll()
 * @method Tuteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TuteurRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Tuteur::class);
    }

    /**
     * @param $keyword
     *
     * @return Tuteur[]
     */
    public function search(?string $keyword, bool $archived = false): array
    {
        return $this->createQueryBuilder('tuteur')
            ->leftJoin('tuteur.relations', 'relations', 'WITH')
            ->addSelect('relations')
            ->andWhere('tuteur.nom LIKE :keyword OR tuteur.prenom LIKE :keyword OR tuteur.email_conjoint LIKE :keyword OR tuteur.email LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->andwhere('tuteur.archived = :archive')
            ->setParameter('archive', $archived)
            ->addOrderBy('tuteur.nom', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @return Tuteur[]
     */
    public function findSansEnfants(): array
    {
        return $this->createQueryBuilder('tuteur')
            ->andWhere('tuteur.relations IS EMPTY')
            ->getQuery()->getResult();
    }

    public function findForAssociateParent(): QueryBuilder
    {
        return $this->createQueryBuilder('tuteur')
            ->orderBy('tuteur.nom');
    }

    public function findOneByEmail(string $email): ?Tuteur
    {
        return $this->createQueryBuilder('tuteur')
            ->andWhere('tuteur.email = :email or tuteur.email_conjoint = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array|Tuteur[]
     */
    public function getTuteursByUser(User $user): array
    {
        return $this->createQueryBuilder('tuteur')
            ->leftJoin('tuteur.users', 'users', 'WITH')
            ->andWhere(':user MEMBER OF tuteur.users')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tuteur[]
     */
    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('tuteur')
            ->orderBy('tuteur.nom')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tuteur[]
     */
    public function findAllActif(): array
    {
        return $this->createQueryBuilder('tuteur')
            ->andWhere('tuteur.archived = 0')
            ->orderBy('tuteur.nom')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tuteur[]
     */
    public function findDoublon(): array
    {
        return $this->createQueryBuilder('tuteur')
            ->select('count(tuteur.nom) as lignes, tuteur.nom, tuteur.prenom')
            ->addGroupBy('tuteur.nom')
            ->addGroupBy('tuteur.prenom')
            ->getQuery()->getResult();
    }
}
