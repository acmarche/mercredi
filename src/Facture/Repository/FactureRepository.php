<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Facture::class);
    }

    /**
     * @return Facture[]
     */
    public function findFacturesByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('facture')
            ->leftJoin('facture.tuteur', 'tuteur', 'WITH')
            ->addSelect('tuteur')
            ->andWhere('facture.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @return Facture[]
     */
    public function search(?string $tuteur, ?Ecole $ecole, ?bool $paye, ?string $monthYear = null): array
    {
        $queryBuilder = $this->createQueryBuilder('facture')
            ->leftJoin('facture.tuteur', 'tuteur', 'WITH')
            ->leftJoin('facture.facturePresences', 'facturePresences', 'WITH')
            ->leftJoin('facturePresences.presence', 'presence', 'WITH')
            ->leftJoin('presence.enfant', 'enfantE', 'WITH')
            ->leftJoin('facture.factureAccueils', 'factureAccueils', 'WITH')
            ->leftJoin('factureAccueils.accueil', 'accueil', 'WITH')
            ->leftJoin('accueil.enfant', 'enfantA', 'WITH')
            ->addSelect('tuteur', 'facturePresences', 'factureAccueils', 'presence', 'enfantA', 'enfantE', 'accueil');

        if ($tuteur) {
            $queryBuilder->andWhere('tuteur.nom LIKE :tuteur')
                ->setParameter('tuteur', '%'.$tuteur.'%');
        }

        if ($ecole !== null) {
            $queryBuilder->andWhere('enfantE.ecole = :ecole OR enfantA.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($monthYear !== null) {
            $queryBuilder->andWhere('facture.month = :date')
                ->setParameter('date', $monthYear);
        }

        switch ($paye) {
            case true:
                $queryBuilder->andWhere('facture.payeLe IS NOT NULL');

                break;
            case false:
                $queryBuilder->andWhere('facture.payeLe IS NULL');

                break;
            default:
                break;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function remove(Facture $facture): void
    {
        $this->_em->remove($facture);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Facture $facture): void
    {
        $this->_em->persist($facture);
    }
}
