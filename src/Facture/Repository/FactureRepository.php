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
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
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

    public function remove(Facture $facture)
    {
        $this->_em->remove($facture);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Facture $facture)
    {
        $this->_em->persist($facture);
    }
}
