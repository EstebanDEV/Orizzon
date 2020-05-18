<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class UserEventRepository extends EntityRepository 
{
    public function searchParticipate($idUser, $event) 
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT ue
             FROM App\Entity\UserEvent ue
             WHERE ue.participant = :idUser
             AND ue.event = :event'
        )->setParameters(array('idUser' => $idUser, 'event' => $event));

        return $q->getResult();
    }

    public function oneEvent($id, $date)
    {
        return $this->createQueryBuilder('ue')
            ->where('ue.participant = :id')
            ->setParameters(array('id' => $id))
            ->getQuery()
            ->getOneOrNullResult();
    }
}