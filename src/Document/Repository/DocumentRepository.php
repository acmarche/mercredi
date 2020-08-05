<?php

namespace AcMarche\Mercredi\Document\Repository;

use AcMarche\Mercredi\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * @param $keyword
     *
     * @return Document[]
     */
    public function search(?string $keyword): array
    {
        return $this->createQueryBuilder('document')
            ->andWhere('document.nom LIKE :keyword OR document.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->addOrderBy('document.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function remove(Document $document): void
    {
        $this->_em->remove($document);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Document $document): void
    {
        $this->_em->persist($document);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('document')
            ->orderBy('document.nom', 'ASC');
    }
}
