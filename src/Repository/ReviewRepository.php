<?php
// src/Repository/ReviewRepository.php
namespace App\Repository;

use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    // Encuentra las publicaciones de los usuarios seguidos
    public function findReviewsByUsers($users)
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.user', 'u') // Asumiendo que cada publicación tiene un usuario relacionado
            ->where('u IN (:users)')
            ->setParameter('users', $users)
            ->getQuery()
            ->getResult();
    }
}
