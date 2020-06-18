<?php

namespace AcMarche\Mercredi\User\Repository;

use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOneByEmailOrUserName(string $username): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :username OR user.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @param string $role
     *
     * @return User[]
     */
    public function findByNameOrRoles(?string $name, ?string $role): array
    {
        $qb = $this->createQueryBuilder('user');

        if ($name) {
            $qb->andWhere('user.nom LIKE :nom OR user.prenom LIKE :nom OR user.email LIKE :nom ')
                ->setParameter('nom', '%'.$name.'%');
        }

        if ($role) {
            $qb->andWhere('user.roles LIKE :role')
                ->setParameter('role', '%'.$role.'%');
        }

        return $qb
            ->addOrderBy('user.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function persist(User $user)
    {
        $this->getEntityManager()->persist($user);
    }

    public function insert(User $user)
    {
        $this->persist($user);
        $this->flush();
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(User $user)
    {
        $this->_em->remove($user);
    }
}
