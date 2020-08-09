<?php

namespace AcMarche\Mercredi\User\Repository;

use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use function get_class;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    /**
     * @return User[]
     */
    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('user')
            ->addOrderBy('user.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
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
     * @return User[]
     */
    public function findByNameOrRoles(?string $name, ?string $role): array
    {
        $queryBuilder = $this->createQueryBuilder('user');

        if ($name) {
            $queryBuilder->andWhere('user.nom LIKE :nom OR user.prenom LIKE :nom OR user.email LIKE :nom ')
                ->setParameter('nom', '%'.$name.'%');
        }

        if ($role) {
            $queryBuilder->andWhere('user.roles LIKE :role')
                ->setParameter('role', '%'.$role.'%');
        }

        return $queryBuilder
            ->addOrderBy('user.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function persist(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function insert(User $user): void
    {
        $this->persist($user);
        $this->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(User $user): void
    {
        $this->_em->remove($user);
    }
}
