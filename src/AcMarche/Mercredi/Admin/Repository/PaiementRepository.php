<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paiement|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]|null findAll()
 * @method Paiement[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function insert(Paiement $paiement)
    {
        $this->_em->persist($paiement);
        $this->save();
    }

    public function remove(Paiement $paiement)
    {
        $this->_em->remove($paiement);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    /**
     * Retourne les paiments lies a l'enfant tuteur.
     *
     * @return Paiement[]
     */
    public function getByEnfantTuteur(EnfantTuteur $enfant_tuteur, $date = null)
    {
        $enfant_id = $enfant_tuteur->getEnfant()->getId();
        $tuteur_id = $enfant_tuteur->getTuteur()->getId();

        $args = ['enfant_id' => $enfant_id, 'tuteur_id' => $tuteur_id];
        if ($date) {
            $args['date'] = $date;
        }

        $paiements = $this->search($args);

        return $paiements;
    }

    /**
     * @param [] $args
     *
     * @return Paiement[]|Paiement
     */
    public function search($args)
    {
        $tuteur_id = isset($args['tuteur_id']) ? $args['tuteur_id'] : null;
        $enfant_id = isset($args['enfant_id']) ? $args['enfant_id'] : 0;
        $date = isset($args['date']) ? $args['date'] : null;
        $cloture = isset($args['cloture']) ? $args['cloture'] : null;
        $one = isset($args['one']) ? $args['one'] : false;

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

        if (1 == $cloture) {
            $qb->andwhere('p.cloture = :cloture')
                ->setParameter('cloture', 1);
        } elseif (-1 == $cloture) {
            $qb->andwhere('p.cloture = :cloture')
                ->setParameter('cloture', 0);
        }

        $qb->orderBy('p.date_paiement', 'DESC');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList(Tuteur $tuteur = null)
    {
        $qb = $this->createQueryBuilder('p');

        if ($tuteur) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur);
        }

        $qb->andwhere('p.cloture = :cloture')
            ->setParameter('cloture', 0);

        $qb->orderBy('p.date_paiement', 'DESC');

        return $qb;
    }
}
