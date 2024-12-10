<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserFollowerRepository
{
    private $connection;

    public function __construct(ManagerRegistry $registry)
    {
        $this->connection = $registry->getConnection();
    }

    public function follow(int $userId, int $followerId): void
    {
        $query = 'INSERT INTO user_followers (user_id, follower_id) VALUES (:userId, :followerId)';
        $this->connection->executeQuery($query, ['userId' => $userId, 'followerId' => $followerId]);
    }

    public function unfollow(int $userId, int $followerId): void
    {
        $query = 'DELETE FROM user_followers WHERE user_id = :userId AND follower_id = :followerId';
        $this->connection->executeQuery($query, ['userId' => $userId, 'followerId' => $followerId]);
    }

    public function isFollowing(int $userId, int $followerId): bool
    {
        $query = 'SELECT COUNT(*) FROM user_followers WHERE user_id = :userId AND follower_id = :followerId';
        return (bool) $this->connection->fetchOne($query, ['userId' => $userId, 'followerId' => $followerId]);
    }

    public function getFollowers(int $userId): array
    {
        $query = 'SELECT follower_id FROM user_followers WHERE user_id = :userId';
        return $this->connection->fetchAllAssociative($query, ['userId' => $userId]);
    }

    public function getFollowing(int $followerId): array
    {
        $query = 'SELECT user_id FROM user_followers WHERE follower_id = :followerId';
        return $this->connection->fetchAllAssociative($query, ['followerId' => $followerId]);
    }
}
