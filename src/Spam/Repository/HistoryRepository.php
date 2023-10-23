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

}
