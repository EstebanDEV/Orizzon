<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class ReqUserPrivateRepository extends EntityRepository 
{
    public function searchReq($idUser, $userSelect) 
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT r
             FROM App\Entity\ReqUserPrivate r
             WHERE r.user = :idUser
             AND r.userTarget = :userSelect'
        )->setParameters(array('idUser' => $idUser, 'userSelect' => $userSelect));

        return $q->getResult();
    }
}