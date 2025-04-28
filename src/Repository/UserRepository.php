<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Recherche avancée des utilisateurs avec filtres et tri
     *
     * @param array $criteria Les critères de recherche
     * @return QueryBuilder
     */
    public function findBySearchCriteria(array $criteria = []): QueryBuilder
    {
        // Créer un nouveau QueryBuilder
        $qb = $this->createQueryBuilder('u');

        // Recherche par terme (nom, prénom, email)
        if (!empty($criteria['search'])) {
            $searchTerm = '%' . $criteria['search'] . '%';
            $qb->andWhere('(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR u.cin LIKE :search)')
               ->setParameter('search', $searchTerm);
        }

        // Filtre par rôle
        if (!empty($criteria['role'])) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $criteria['role']);
        }

        // Filtre par statut de vérification
        if (isset($criteria['is_verified']) && $criteria['is_verified'] !== '') {
            $qb->andWhere('u.isVerified = :is_verified')
               ->setParameter('is_verified', (bool)$criteria['is_verified']);
        }

        // Appliquer le tri de manière sécurisée
        $this->applySorting($qb, $criteria);

        return $qb;
    }

    /**
     * Applique le tri au QueryBuilder de manière sécurisée
     *
     * @param QueryBuilder $qb
     * @param array $criteria
     * @return void
     */
    private function applySorting(QueryBuilder $qb, array $criteria): void
    {
        // Tri par défaut
        $qb->orderBy('u.id', 'DESC');

        // Si aucun critère de tri n'est spécifié, on utilise le tri par défaut
        if (empty($criteria['sort_by'])) {
            return;
        }

        try {
            // Valider les critères de tri
            $validSortFields = ['name', 'salary'];
            $validSortDirections = ['asc', 'desc'];

            $sortBy = strtolower(trim($criteria['sort_by']));
            $sortDir = !empty($criteria['sort_dir']) ? strtolower(trim($criteria['sort_dir'])) : 'asc';

            // Vérifier si les valeurs sont valides
            if (!in_array($sortBy, $validSortFields)) {
                return; // Garder le tri par défaut
            }

            if (!in_array($sortDir, $validSortDirections)) {
                $sortDir = 'asc'; // Direction par défaut si non valide
            }

            $direction = ($sortDir === 'desc') ? 'DESC' : 'ASC';

            // Appliquer le tri en fonction du champ
            if ($sortBy === 'salary') {
                $qb->orderBy('u.salary', $direction);
            } elseif ($sortBy === 'name') {
                $qb->orderBy('u.last_name', $direction)
                   ->addOrderBy('u.first_name', $direction);
            }
        } catch (\Exception $e) {
            // En cas d'erreur, le tri par défaut est déjà appliqué
            // En production, vous pouvez activer cette ligne pour le logging
            // error_log('Erreur lors du tri: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les statistiques des utilisateurs par rôle
     *
     * @return array
     */
    public function getUserStatsByRole(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.role, COUNT(u.id) as count')
            ->groupBy('u.role');

        $results = $qb->getQuery()->getResult();

        $stats = [];
        foreach ($results as $result) {
            $stats[$result['role']] = $result['count'];
        }

        return $stats;
    }

    /**
     * Récupère la moyenne des salaires par rôle
     *
     * @return array
     */
    public function getAverageSalaryByRole(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.role, AVG(u.salary) as average_salary')
            ->groupBy('u.role');

        $results = $qb->getQuery()->getResult();

        $stats = [];
        foreach ($results as $result) {
            $stats[$result['role']] = round($result['average_salary'], 2);
        }

        return $stats;
    }

    /**
     * Trouve tous les utilisateurs avec un rôle spécifique
     *
     * @param string $role Le rôle à rechercher
     * @return User[] Un tableau d'utilisateurs
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'utilisateurs en attente d'activation
     *
     * @return int Le nombre d'utilisateurs en attente
     */
    public function countPendingUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.isVerified = :isVerified')
            ->setParameter('isVerified', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
