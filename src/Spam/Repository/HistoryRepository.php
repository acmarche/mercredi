<?php

namespace AcMarche\Mercredi\Spam\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method History|null   find($id, $lockMode = null, $lockVersion = null)
 * @method History|null   findOneBy(array $criteria, array $orderBy = null)
 * @method History[]|null findAll()
 * @method History[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class HistoryRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, History::class);
    }

    public function findBySubject(string $subject): ?History
    {
        return $this->createQueryBuilder('spam')
            ->andWhere('spam.subject = :subject')
            ->setParameter('subject', $subject)
            ->getQuery()->getOneOrNullResult();
    }

    public function findBySubjectAndDate(string $subject, \DateTime $today): ?History
    {
        return $this->createQueryBuilder('spam')
            ->andWhere('spam.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('spam.created_at LIKE :date')
            ->setParameter('date', $today->format('Y-m-d'))
            ->getQuery()->getOneOrNullResult();
    }
}
