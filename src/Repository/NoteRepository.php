<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * Trouve toutes les notes pour une formation donnée
     */
    public function findByFormation(Formation $formation)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.formation = :formation')
            ->setParameter('formation', $formation)
            ->orderBy('n.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule la note moyenne d'une formation
     */
    public function calculateAverageRating(Formation $formation): float
    {
        $result = $this->createQueryBuilder('n')
            ->select('AVG(n.valeur) as average')
            ->andWhere('n.formation = :formation')
            ->setParameter('formation', $formation)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round((float)$result, 1) : 0;
    }

    /**
     * Compte le nombre de notes pour une formation
     */
    public function countRatings(Formation $formation): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.formation = :formation')
            ->setParameter('formation', $formation)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Vérifie si un utilisateur a déjà noté une formation
     */
    public function hasUserRated(Formation $formation, int $userId): bool
    {
        $count = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.formation = :formation')
            ->andWhere('n.user = :userId')
            ->setParameter('formation', $formation)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
