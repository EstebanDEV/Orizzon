<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class EventRepository extends EntityRepository 
{
    public function findEvents($offset, $limit, $date) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT e
             FROM App\Entity\Event e
             WHERE UNIX_TIMESTAMP(e.dateCreated) < :date
             ORDER BY e.dateCreated DESC'
        )
        ->setParameter('date', $date);
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findMyEvents($id, $offset, $limit, $date) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT e
             FROM App\Entity\Event e
             WHERE UNIX_TIMESTAMP(e.dateCreated) < :date
             AND e.user = :id
             ORDER BY e.dateCreated DESC'
        )
        ->setParameters(array('id' => $id, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function mapEvents($verif) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT e
             FROM App\Entity\Event e
             WHERE e.map = 1
             AND UNIX_TIMESTAMP(e.date) > UNIX_TIMESTAMP()
             AND UNIX_TIMESTAMP(e.date) < :verif'
        )
        ->setParameter('verif', $verif);

        return $q->getResult();
    }

    public function eventProfile($id)
    {
        return $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}