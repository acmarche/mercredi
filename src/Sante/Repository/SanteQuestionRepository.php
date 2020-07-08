<?php

namespace AcMarche\Mercredi\Sante\Repository;

use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SanteQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method SanteQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method SanteQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanteQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SanteQuestion::class);
    }

    public function findAll()
    {
        return $this->findBy([], ['display_order' => 'ASC']);
    }

    /**
     * @param SanteQuestion[] $questionsRepondues
     *
     * @return SanteQuestion[]
     */
    public function getQuestionsNonRepondues($questionsRepondues)
    {
        $qb = $this->createQueryBuilder('santeQuestion');

        if (\count($questionsRepondues) > 0) {
            $qb->andWhere('santeQuestion.id NOT IN (:questions) ')
                ->setParameter('questions', $questionsRepondues);
        }

        $qb->addOrderBy('santeQuestion.display_order');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function persist(SanteQuestion $santeQuestion)
    {
        $this->_em->persist($santeQuestion);
    }

    public function remove(SanteQuestion $santeQuestion)
    {
        $this->_em->remove($santeQuestion);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
