<?php

namespace App\Repository;
 
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
 
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchUsers($content, $offset, $limit): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
             FROM App\Entity\User u
             WHERE u.username LIKE :content
             OR u.name LIKE :content
             ORDER BY u.score DESC'
        )->setParameter('content', $content);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getResult();
    }

    public function classementPart(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
             'SELECT u
             FROM App\Entity\User u
             WHERE u.type = 0
             ORDER BY u.score DESC'
        );
        $query->setMaxResults(3);

        return $query->getResult();
    }

    public function classementOrga(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
             'SELECT u
             FROM App\Entity\User u
             WHERE u.type = 1
             ORDER BY u.score DESC'
        );
        $query->setMaxResults(3);

        return $query->getResult();
    }
}