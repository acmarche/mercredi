<?php

namespace AcMarche\Mercredi\Animateur\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Animateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animateur[]    findAll()
 * @method Animateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AnimateurRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Animateur::class);
    }

    public function findOneByEmail(string $email): ?Animateur
    {
        return $this
            ->createQueryBuilder('animateur')
            ->andWhere('animateur.email = :email')
            ->setParameter('email', $email)
            ->addOrderBy('animateur.nom', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $keyword
     *
     * @return Animateur[]
     */
    public function search(?string $keyword): array
    {
        return $this
            ->createQueryBuilder('animateur')
            ->andWhere('animateur.nom LIKE :keyword OR animateur.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->addOrderBy('animateur.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('animateur')
            ->orderBy('animateur.nom', 'ASC');
    }

    public function getAnimateursByUser(User $user)
    {
        return $this
            ->createQueryBuilder('animateur')
            ->leftJoin('animateur.users', 'users', 'WITH')
            ->andWhere(':user MEMBER OF animateur.users')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findForAssociateAnimateur(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('animateur')
            ->orderBy('animateur.nom');
    }
}
