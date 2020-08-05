<?php

namespace AcMarche\Mercredi\Sante\Repository;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
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

    public function getResponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion): ?SanteReponse
    {
        return $this->createQueryBuilder('reponse')
            ->andWhere('reponse.sante_fiche = :fiche')
            ->setParameter('fiche', $santeFiche)
            ->andWhere('reponse.question = :question')
            ->setParameter('question', $santeQuestion)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return SanteReponse[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySanteFiche(SanteFiche $santeFiche): array
    {
        return $this->createQueryBuilder('reponse')
            ->andWhere('reponse.sante_fiche = :fiche')
            ->setParameter('fiche', $santeFiche)
            ->getQuery()->getResult();
    }

    public function persist(SanteReponse $santeReponse): void
    {
        $this->_em->persist($santeReponse);
    }

    public function remove(SanteReponse $santeReponse): void
    {
        $this->_em->remove($santeReponse);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
