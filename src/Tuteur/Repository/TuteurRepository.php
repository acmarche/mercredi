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
class TuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tuteur::class);
    }

    /**
     * @param $keyword
     *
     * @return Tuteur[]
     */
    public function search(?string $keyword): array
    {
        return $this->createQueryBuilder('tuteur')
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
    public function findSansEnfants()
    {
        $qb = $this->createQueryBuilder('tuteur')
            ->andWhere('tuteur.relations IS EMPTY')
            ->getQuery()->getResult();

        return $qb;
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

    public function getTuteursByUser(User $user): array
    {
        return $this->createQueryBuilder('tuteur')
            ->leftJoin('tuteur.users', 'users', 'WITH')
            ->andWhere(':user MEMBER OF tuteur.users')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function remove(Tuteur $tuteur)
    {
        $this->_em->remove($tuteur);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Tuteur $tuteur)
    {
        $this->_em->persist($tuteur);
    }

}
