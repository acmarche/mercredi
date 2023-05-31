<?php

namespace AcMarche\Mercredi\Spam\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Spam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Spam|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Spam|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Spam[]|null findAll()
 * @method Spam[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SpamRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Spam::class);
    }

    public function findBySubject(string $subject): ?Spam
    {
        return $this->createQueryBuilder('spam')
            ->andWhere('spam.subject = :subject')
            ->setParameter('subject', $subject)
            ->getQuery()->getOneOrNullResult();
    }

    public function findBySubjectAndDate(string $subject, \DateTime $today): ?Spam
    {
        return $this->createQueryBuilder('spam')
            ->andWhere('spam.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('spam.created_at LIKE :date')
            ->setParameter('date', $today->format('Y-m-d'))
            ->getQuery()->getOneOrNullResult();
    }
}
