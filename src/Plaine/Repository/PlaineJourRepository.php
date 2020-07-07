<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Entity\Jour;
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

    /**
     * @param Plaine $plaine
     * @return PlaineJour[]
     */
    public function findByPlaine(Plaine $plaine): array
    {
        return $this->createQueryBuilder('plaine_jour')
            ->leftJoin('plaine_jour.jour', 'jour', 'WITH')
            ->addSelect('jour')
            ->andWhere('plaine_jour.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->addOrderBy('jour.date_jour', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPlaineAndJour(Plaine $plaine, ?Jour $jour): ?PlaineJour
    {
        return $this->createQueryBuilder('plaine_jour')
            ->andWhere('plaine_jour.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->andWhere('plaine_jour.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()
            ->getOneOrNullResult();
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
