<?php

namespace AcMarche\Mercredi\Accueil\Repository;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Accueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accueil[]    findAll()
 * @method Accueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accueil::class);
    }

    public function getQbForListing(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('accueil')
            ->orderBy('accueil.date_jour', 'ASC');

        return $qb;
    }

    public function findOneByDateEnfant(\DateTimeInterface $date, Enfant $enfant): ?Accueil
    {
        return $this->createQueryBuilder('accueil')
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $date)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Enfant $enfant
     * @return Accueil[]
     */
    public function findByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder('accueil')
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    public function remove(Accueil $accueil)
    {
        $this->_em->remove($accueil);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Accueil $accueil)
    {
        $this->_em->persist($accueil);
    }

}
