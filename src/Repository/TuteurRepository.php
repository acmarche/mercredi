<?php

namespace AcMarche\Mercredi\Repository;

use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tuteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tuteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tuteur[]    findAll()
 * @method Tuteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tuteur::class);
    }


    public function remove(Tuteur $tuteur)
    {
        $this->_em->remove($tuteur);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Tuteur $tuteur)
    {
        $this->_em->persist($tuteur);
    }
}
