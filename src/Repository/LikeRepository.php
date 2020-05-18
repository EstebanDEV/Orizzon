<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class LikeRepository extends EntityRepository 
{
    public function searchLike($idUser, $idPost) 
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT l 
             FROM App\Entity\Like l
             WHERE l.user = :idUser
             AND l.post = :idPost'
        )->setParameters(array('idUser' => $idUser, 'idPost' => $idPost));

        return $q->getResult();
    }
}