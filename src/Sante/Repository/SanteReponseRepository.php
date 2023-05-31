<?php

namespace AcMarche\Mercredi\Sante\Repository;

use AcMarche\Mercredi\Doctrine\OrmCrudTrait;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Sante\SanteReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SanteReponse|null   find($id, $lockMode = null, $lockVersion = null)
 * @method SanteReponse|null   findOneBy(array $criteria, array $orderBy = null)
 * @method SanteReponse[]|null findAll()
 * @method SanteReponse[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SanteReponseRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SanteReponse::class);
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
     * @throws NonUniqueResultException
     */
    public function findBySanteFiche(SanteFiche $santeFiche): array
    {
        return $this->createQueryBuilder('reponse')
            ->andWhere('reponse.sante_fiche = :fiche')
            ->setParameter('fiche', $santeFiche)
            ->getQuery()->getResult();
    }
}
