<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineJour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaineJour|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineJour|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineJour[]    findAll()
 * @method PlaineJour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineJourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaineJour::class);
    }

    public function findByPlaine(Plaine $plaine): array
    {
        return $this->createQueryBuilder('plaine')
            ->andWhere('plaine.inscriptionOpen = 1')
            ->getQuery()
            ->getResult();
    }

    public function remove(PlaineJour $plaine)
    {
        $this->_em->remove($plaine);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(PlaineJour $plaine)
    {
        $this->_em->persist($plaine);
    }

}
