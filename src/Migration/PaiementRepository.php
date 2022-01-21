<?php

namespace AcMarche\Mercredi\Migration;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @deprecated  for migration
 *
 * @method Paiement|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]|null findAll()
 * @method Paiement[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    /**
     * Retourne les paiments lies a l'enfant tuteur.
     *
     * @return Paiement[]
     */
    public function getByEnfantTuteur(EnfantTuteur $enfant_tuteur, $date = null): Paiement|array
    {
        $enfant_id = $enfant_tuteur->getEnfant()->getId();
        $tuteur_id = $enfant_tuteur->getTuteur()->getId();

        $args = [
            'enfant_id' => $enfant_id,
            'tuteur_id' => $tuteur_id,
        ];
        if ($date) {
            $args['date'] = $date;
        }

        return $this->search($args);
    }

    /**
     * @param [] $args
     *
     * @return Paiement[]|Paiement
     */
    public function search($args): array|Paiement
    {
        $tuteur_id = $args['tuteur_id'] ?? null;
        $enfant_id = $args['enfant_id'] ?? 0;
        $date = $args['date'] ?? null;
        $cloture = $args['cloture'] ?? null;
        $one = $args['one'] ?? false;

        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.tuteur', 't', 'WITH');
        $qb->leftJoin('p.enfant', 'e', 'WITH');
        $qb->leftJoin('p.presences', 'pe', 'WITH');
        $qb->leftJoin('p.plaine_presences', 'plaine_presences', 'WITH');
        $qb->addSelect('p', 't', 'e', 'pe', 'plaine_presences');

        if ($tuteur_id) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur_id);
        }

        if ($enfant_id) {
            $qb->andwhere('p.enfant = :enfant')
                ->setParameter('enfant', $enfant_id);
        }

        if ($date) {
            $qb->andwhere('p.date_paiement LIKE :date')
                ->setParameter('date', '%'.$date.'%');
        }

        if (1 === $cloture) {
            $qb->andwhere('p.cloture = :cloture')
                ->setParameter('cloture', 1);
        } elseif (-1 === $cloture) {
            $qb->andwhere('p.cloture = :cloture')
                ->setParameter('cloture', 0);
        }

        $qb->orderBy('p.date_paiement', 'DESC');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        return $query->getResult();
    }

    public function getForList(Tuteur $tuteur = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if (null !== $tuteur) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur);
        }

        $qb->andwhere('p.cloture = :cloture')
            ->setParameter('cloture', 0);

        $qb->orderBy('p.date_paiement', 'DESC');

        return $qb;
    }
}
