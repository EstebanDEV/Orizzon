<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql\UnixTimestamp;
 
class CommentRepository extends EntityRepository 
{
    public function findComments($id, $offset, $limit, $date) : array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT c
             FROM App\Entity\Comment c
             WHERE UNIX_TIMESTAMP(c.date) < :date
             AND c.post = :id
             ORDER BY c.date DESC'
        )->setParameters(array('id' => $id, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }
}