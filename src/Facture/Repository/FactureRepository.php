<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FactureRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Facture::class);
    }

    public function getQBl(): QueryBuilder
    {
        return $this->createQueryBuilder('facture')
            ->leftJoin('facture.tuteur', 'tuteur', 'WITH')
            ->leftJoin('facture.factureReductions', 'factureReductions', 'WITH')
            ->leftJoin('facture.factureComplements', 'factureComplements', 'WITH')
            ->leftJoin('facture.facturePresences', 'facturePresences', 'WITH')
            ->addSelect('tuteur', 'factureReductions', 'factureComplements', 'facturePresences');
    }

    /**
     * @return Facture[]
     */
    public function findFacturesByTuteur(Tuteur $tuteur): array
    {
        return $this->getQBl()
            ->andWhere('facture.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @return Facture[]
     */
    public function findFacturesByTuteurWhoIsSend(Tuteur $tuteur): array
    {
        return $this->getQBl()
            ->andWhere('facture.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andWhere('facture.envoyeLe IS NOT NULL')
            ->getQuery()->getResult();
    }

    /**
     * @return Facture[]
     */
    public function findFacturesByMonth(string $month): array
    {
        return $this->getQBl()
            ->andWhere('facture.mois = :mois')
            ->setParameter('mois', $month)
            ->getQuery()->getResult();
    }

    /**
     * @return Facture[]
     */
    public function findFacturesByMonthNotSend(string $month): array
    {
        return $this->getQBl()
            ->andWhere('facture.mois = :mois')
            ->setParameter('mois', $month)
            ->andWhere('facture.envoyeLe IS NULL')
            ->getQuery()->getResult();
    }

    /**
     * @return Facture[]
     */
    public function findFacturesByMonthOnlyPaper(string $month): array
    {
        return $this->getQBl()
            ->andWhere('facture.mois = :mois')
            ->setParameter('mois', $month)
            ->andWhere('tuteur.facture_papier = 1')
            ->getQuery()->getResult();
    }

    /**
     * @return Facture[]
     */
    public function search(
        ?int $numero,
        ?string $tuteur,
        ?string $enfant,
        ?Ecole $ecole,
        ?Plaine $plaine,
        ?bool $paye,
        ?string $monthYear = null,
        ?\DateTimeInterface $datePaiement,
        ?string $communication = null
    ): array {
        $queryBuilder = $this->getQBl();

        if ($numero !== null) {
            $queryBuilder->andWhere('facture.id = :numero')
                ->setParameter('numero', $numero);
        }

        if ($tuteur) {
            $queryBuilder->andWhere('tuteur.nom LIKE :tuteur OR tuteur.prenom LIKE :tuteur')
                ->setParameter('tuteur', '%'.$tuteur.'%');
        }

        if ($enfant) {
            $queryBuilder->andWhere('facturePresences.nom LIKE :enfant OR facturePresences.prenom LIKE :enfant')
                ->setParameter('enfant', '%'.$enfant.'%');
        }

        if ($ecole !== null) {
            $queryBuilder->andWhere('facture.ecoles LIKE :ecole')
                ->setParameter('ecole', '%'.$ecole.'%');
        }

        if ($plaine !== null) {
            $queryBuilder->andWhere('facture.plaine = :plaine')
                ->setParameter('plaine', $plaine->getNom());
        }

        if ($monthYear !== null) {
            $queryBuilder->andWhere('facture.mois = :monthYear')
                ->setParameter('monthYear', $monthYear);
        }

        if ($communication !== null) {
            $queryBuilder->andWhere('facture.communication LIKE :commu')
                ->setParameter('commu', '%'.$communication.'%');
        }

        if ($datePaiement !== null) {
            $queryBuilder->andWhere('facture.payeLe LIKE :datePaiement')
                ->setParameter('datePaiement', $datePaiement->format('Y-m-d').'%');
        }

        if ($paye === false) {
            $queryBuilder->andWhere('facture.payeLe IS NULL');
        }
        if ($paye === true) {
            $queryBuilder->andWhere('facture.payeLe IS NOT NULL');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByTuteurAndPlaine(Tuteur $tuteur, Plaine $plaine): ?Facture
    {
        return $this->getQBl()
            ->andWhere('facture.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andWhere('facture.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->getQuery()->getOneOrNullResult();
    }
}
