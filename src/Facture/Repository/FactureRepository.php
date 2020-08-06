<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FactureRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const TUTEUR = 'tuteur';
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Facture::class);
    }

    /**
     * @param Tuteur $tuteur
     * @return Facture[]
     */
    public function findFacturesByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('facture')
            ->leftJoin('facture.tuteur', self::TUTEUR, 'WITH')
            ->addSelect(self::TUTEUR)
            ->andWhere('facture.tuteur = :tuteur')
            ->setParameter(self::TUTEUR, $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @param string|null $tuteur
     * @param bool|null $paye
     * @return Facture[]
     */
    public function search(?string $tuteur, ?bool $paye): array
    {
        $queryBuilder = $this->createQueryBuilder('facture')
            ->leftJoin('facture.tuteur', self::TUTEUR, 'WITH')
            ->addSelect(self::TUTEUR);

        if ($tuteur) {
            $queryBuilder->andWhere('tuteur.nom LIKE :tuteur')
                ->setParameter(self::TUTEUR, '%'.$tuteur.'%');
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
