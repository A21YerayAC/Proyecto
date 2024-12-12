<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUsersNotFollowed(User $user)
    {
        // Obtener los usuarios que no están en la lista de 'following' del usuario
        $queryBuilder = $this->createQueryBuilder('u')
            ->where(':user NOT MEMBER OF u.following')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }
}