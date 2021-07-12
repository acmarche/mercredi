<?php

namespace AcMarche\Mercredi\Tuteur\Repository;

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
    /**
     * @var string
     */
    private const TUTEUR = 'tuteur';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Tuteur::class);
    }

    /**
     * @param $keyword
     *
     * @return Tuteur[]
     */
    public function search(?string $keyword): array
    {
        return $this->createQueryBuilder(self::TUTEUR)
            ->leftJoin('tuteur.relations', 'relations', 'WITH')
            ->addSelect('relations')
            ->andWhere('tuteur.nom LIKE :keyword OR tuteur.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->addOrderBy('tuteur.nom', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @return Tuteur[]
     */
    public function findSansEnfants(): array
    {
        return $this->createQueryBuilder(self::TUTEUR)
            ->andWhere('tuteur.relations IS EMPTY')
            ->getQuery()->getResult();
    }

    public function findForAssociateParent(): QueryBuilder
    {
        return $this->createQueryBuilder(self::TUTEUR)
            ->orderBy('tuteur.nom');
    }

    public function findOneByEmail(string $email): ?Tuteur
    {
        return $this->createQueryBuilder(self::TUTEUR)
            ->andWhere('tuteur.email = :email or tuteur.email_conjoint = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTuteursByUser(User $user): array
    {
        return $this->createQueryBuilder(self::TUTEUR)
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
        return $this->createQueryBuilder(self::TUTEUR)
            ->orderBy('tuteur.nom')
            ->getQuery()
            ->getResult();
    }

    public function remove(Tuteur $tuteur): void
    {
        $this->_em->remove($tuteur);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Tuteur $tuteur): void
    {
        $this->_em->persist($tuteur);
    }

}
