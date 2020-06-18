<?php

namespace AcMarche\Mercredi\Page\Repository;

use AcMarche\Mercredi\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function remove(Page $page)
    {
        $this->_em->remove($page);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Page $page)
    {
        $this->_em->persist($page);
    }
}
