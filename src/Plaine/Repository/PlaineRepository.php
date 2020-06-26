<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Plaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Plaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plaine[]    findAll()
 * @method Plaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plaine::class);
    }

    public function remove(Plaine $plaine)
    {
        $this->_em->remove($plaine);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Plaine $plaine)
    {
        $this->_em->persist($plaine);
    }

    public function getQbForListing(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('plaine')
            ->orderBy('plaine.nom', 'ASC');

        return $qb;
    }
}
