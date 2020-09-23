<?php

namespace AcMarche\Mercredi\Accueil\Repository;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Accueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accueil[]    findAll()
 * @method Accueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AccueilRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const ACCUEIL = 'accueil';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Accueil::class);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ACCUEIL)
            ->orderBy('accueil.date_jour', 'ASC');
    }

    public function isRegistered(Accueil $accueil, Enfant $enfant): ?Accueil
    {
        return $this->createQueryBuilder(self::ACCUEIL)
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $accueil->getDateJour()->format('Y-m-d'))
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $accueil->getHeure())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder(self::ACCUEIL)
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByEnfantAndHeure(Enfant $enfant, string $heure): array
    {
        return $this->createQueryBuilder(self::ACCUEIL)
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $heure)
            ->getQuery()->getResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder(self::ACCUEIL)
            ->andWhere('accueil.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    public function remove(Accueil $accueil): void
    {
        $this->_em->remove($accueil);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Accueil $accueil): void
    {
        $this->_em->persist($accueil);
    }
}
