<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class MessageRepository extends EntityRepository 
{
    public function findDiscussions($id, $offset, $limit, $date) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE m.userReceiveMsg = :id
             AND UNIX_TIMESTAMP(m.date) < :date
             GROUP BY m.user
             ORDER BY m.date DESC'
        )
        ->setParameters(array('id' => $id, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findLastMessage($id, $idSelect, $limit, $date) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE m.user = :idSelect
             AND m.userReceiveMsg = :id
             AND UNIX_TIMESTAMP(m.date) < :date
             ORDER BY m.date DESC'
        )
        ->setParameters(array('id' => $id, 'idSelect' => $idSelect, 'date' => $date));
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findMessagesLength($idUser, $idSelect) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE m.user = :idUser 
             AND m.userReceiveMsg = :idSelect
             OR m.user = :idSelect
             AND m.userReceiveMsg = :idUser'           
        )->setParameters(array('idUser' => $idUser, 'idSelect' => $idSelect));

        return $q->getResult();
    }

    public function findMessages($idUser, $idSelect, $offset, $limit, $date) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE UNIX_TIMESTAMP(m.date) < :date
             AND m.user = :idUser 
             AND m.userReceiveMsg = :idSelect
             OR  UNIX_TIMESTAMP(m.date) < :date
             AND m.user = :idSelect
             AND m.userReceiveMsg = :idUser           
             ORDER BY m.date DESC'
        )->setParameters(array('idUser' => $idUser, 'idSelect' => $idSelect, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findBeforeMessages($idUser, $idSelect, $offset, $limit, $first) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE m.id < :first
             AND m.user = :idSelect 
             AND m.userReceiveMsg = :idUser
             OR m.id < :first
             AND m.user = :idUser 
             AND m.userReceiveMsg = :idSelect
             ORDER BY m.date DESC'         
        )->setParameters(array('idUser' => $idUser, 'idSelect' => $idSelect, 'first' => $first));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findMessagesUserSelect($idUser, $idSelect, $last) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT m
             FROM App\Entity\Message m
             WHERE m.id > :last
             AND m.user = :idSelect 
             AND m.userReceiveMsg = :idUser
             ORDER BY m.date DESC'         
        )->setParameters(array('idUser' => $idUser, 'idSelect' => $idSelect, 'last' => $last));

        return $q->getResult();
    }
}