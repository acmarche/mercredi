<?php

namespace AcMarche\Mercredi\Sante\Repository;

use AcMarche\Mercredi\Entity\Sante\SanteReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SanteReponse|null   find($id, $lockMode = null, $lockVersion = null)
 * @method SanteReponse|null   findOneBy(array $criteria, array $orderBy = null)
 * @method SanteReponse[]|null findAll()
 * @method SanteReponse[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanteReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SanteReponse::class);
    }

    public function getBySanteFiche(iterable $santeFiches)
    {
        $qb = $this->createQueryBuilder('sante_reponse');

        $qb->andWhere('sante_reponse.sante_fiche IN (:fiches)')
            ->setParameter('fiches', $santeFiches);

        return $qb->getQuery()->getResult();
    }

    public function persist(SanteReponse $santeReponse)
    {
        $this->_em->persist($santeReponse);
    }

    public function remove(SanteReponse $santeReponse)
    {
        $this->_em->remove($santeReponse);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
