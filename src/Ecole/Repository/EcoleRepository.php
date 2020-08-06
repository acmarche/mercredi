<?php

namespace AcMarche\Mercredi\Ecole\Repository;

use AcMarche\Mercredi\Entity\Ecole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Ecole|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecole|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecole[]    findAll()
 * @method Ecole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EcoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Ecole::class);
    }

    public function remove(Ecole $ecole): void
    {
        $this->_em->remove($ecole);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Ecole $ecole): void
    {
        $this->_em->persist($ecole);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('ecole')
            ->orderBy('ecole.nom', 'ASC');
    }
}
