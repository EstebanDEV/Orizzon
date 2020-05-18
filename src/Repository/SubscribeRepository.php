<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class SubscribeRepository extends EntityRepository 
{
    public function searchSubscribe($idUser, $userSelect) 
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT s
             FROM App\Entity\Subscribe s
             WHERE s.user = :idUser
             AND s.subscription = :userSelect'
        )->setParameters(array('idUser' => $idUser, 'userSelect' => $userSelect));

        return $q->getResult();
    }

    public function findFollowers($id, $offset, $limit, $date) 
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT s
             FROM App\Entity\Subscribe s
             WHERE s.subscription = :id
             AND UNIX_TIMESTAMP(s.date) < :date
             ORDER BY s.date DESC'
        )
        ->setParameters(array('id' => $id, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findFollowings($id, $offset, $limit, $date) 
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT s
             FROM App\Entity\Subscribe s
             WHERE s.user = :id
             AND UNIX_TIMESTAMP(s.date) < :date
             ORDER BY s.date DESC'
        )
        ->setParameters(array('id' => $id, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }
}