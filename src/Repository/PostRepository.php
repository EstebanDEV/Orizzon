<?php

namespace App\Repository;
 
use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql\UnixTimestamp;
 
class PostRepository extends EntityRepository 
{
    public function findUserPosts($id, $date, $offset, $limit): array
    {
        $em = $this->getEntityManager();

        $q = $em->createQuery(
            'SELECT p 
             FROM App\Entity\Post p
             WHERE p.author = :id
             AND UNIX_TIMESTAMP(p.date) < :date
             ORDER BY p.date DESC'
        )
        ->setParameters(array('id' => $id, 'date' => $date));
        $q->setFirstResult($offset);
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findFeedPosts($id, $date, $offset, $limit) : array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p')
            ->innerJoin(
                'App\Entity\User',
                'u',
                'WITH',
                'p.author = u.id'
            )
            ->innerJoin(
                'App\Entity\Subscribe',
                's',
                'WITH',
                'u.id = s.subscription and :id = s.user'
            )
            ->where('UNIX_TIMESTAMP(p.date) < :date')
            ->orderBy( 'p.date','DESC' )
            ->setParameters(array('id' => $id, 'date' => $date));
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findPost($id, $author)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.author = :author')
            ->setParameters(array('id' => $id, 'author' => $author))
            ->getQuery()
            ->getOneOrNullResult();
    }
}