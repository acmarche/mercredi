<?php


namespace AcMarche\Mercredi\Message\Repository;


use AcMarche\Mercredi\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function remove(Message $message)
    {
        $this->_em->remove($message);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Message $message)
    {
        $this->_em->persist($message);
    }
}
