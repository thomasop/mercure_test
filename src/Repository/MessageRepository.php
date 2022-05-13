<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Message $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Message $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findMessageByConversationId($conversationId)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->where('m.conversation = :conversationId')
            ->setParameter('conversationId', $conversationId)
        ;

        return $qb->getQuery()->getResult();
    }

    public function last($conversationId)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m')
        ->where('m.conversation = :conversationId')
            ->setParameter('conversationId', $conversationId)
        ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getResult();
    }

    public function new($current, $sender)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->select('n')
        ->where('n.new = true')
        ->andWhere($qb->expr()->orX(
            $qb->expr()->eq('n.user', ':current'),
            $qb->expr()->eq('n.user', ':sender')
        ))
        ->setParameters(new ArrayCollection([
            new Parameter('current', $current),
            new Parameter('sender', $sender)
        ]));
        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
