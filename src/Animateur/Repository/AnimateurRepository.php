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
final class AnimateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Animateur::class);
    }

    /**
     * @param $keyword
     *
     * @return Animateur[]
     */
    public function search(?string $keyword): array
    {
        return $this->createQueryBuilder('animateur')
            ->andWhere('animateur.nom LIKE :keyword OR animateur.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->addOrderBy('animateur.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function remove(Animateur $animateur): void
    {
        $this->_em->remove($animateur);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Animateur $animateur): void
    {
        $this->_em->persist($animateur);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('animateur')
            ->orderBy('animateur.nom', 'ASC');
    }
}
