<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Trouve toutes les notifications non lues pour un utilisateur donné
     */
    public function findUnreadByUser(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->andWhere('n.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les notifications pour un utilisateur donné
     */
    public function findByUser(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'utilisateurs en attente d'activation
     */
    public function countPendingUsers(): int
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQuery(
            'SELECT COUNT(u.id) 
            FROM App\Entity\User u 
            WHERE u.isVerified = :isVerified'
        )->setParameter('isVerified', false);
        
        return (int) $query->getSingleScalarResult();
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsRead(User $user): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', ':isRead')
            ->where('n.user = :user')
            ->andWhere('n.isRead = :notRead')
            ->setParameter('isRead', true)
            ->setParameter('user', $user)
            ->setParameter('notRead', false)
            ->getQuery()
            ->execute();
    }
}
