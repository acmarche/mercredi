<?php

namespace AcMarche\Mercredi\Animateur\Repository;

use AcMarche\Mercredi\Entity\Animateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Animateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animateur[]    findAll()
 * @method Animateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animateur::class);
    }

    public function remove(Animateur $animateur)
    {
        $this->_em->remove($animateur);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Animateur $animateur)
    {
        $this->_em->persist($animateur);
    }

    public function getQbForListing(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('animateur')
            ->orderBy('animateur.nom', 'ASC');

        return $qb;
    }
}
