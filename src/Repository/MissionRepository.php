<?php

namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
/**
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    
    public function searchAndSort(array $filters = [], array $sorting = []): QueryBuilder
{
    $qb = $this->createQueryBuilder('m');
    
    // Filtres
    if (!empty($filters['search'])) {
        $qb->andWhere('m.titre LIKE :search OR m.destination LIKE :search')
           ->setParameter('search', '%'.$filters['search'].'%');
    }
    if (!empty($filters['date_from'])) {
        $qb->andWhere('m.date >= :date_from')
           ->setParameter('date_from', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $qb->andWhere('m.date <= :date_to')
           ->setParameter('date_to', $filters['date_to']);
    }
    
    // Tris multiples
    if (!empty($sorting)) {
        foreach ($sorting as $field => $direction) {
            // Valider les champs autorisés
            if (!in_array($field, ['titre', 'date', 'destination'])) {
                continue; // Ignore invalid sorting fields
            }
            $dir = strtolower($direction) === 'asc' ? 'ASC' : 'DESC'; // Ensures correct sorting direction

            $qb->addOrderBy('m.' . $field, $dir); // Add order to the query
        }
    } else {
        // Tri par défaut
        $qb->orderBy('m.date', 'DESC');
    }
    
    return $qb;
}

    

    /**
     * Recherche avancée des missions avec filtres et tri
     *
     * @param array $criteria Les critères de recherche
     * @return QueryBuilder
     */
    public function findBySearchCriteria(array $criteria = []): QueryBuilder
    {
        // Créer un nouveau QueryBuilder
        $qb = $this->createQueryBuilder('m');

        // Recherche par terme (nom, prénom, email)
        if (!empty($criteria['search'])) {
            $searchTerm = '%' . $criteria['search'] . '%';
            $qb->andWhere('(m.titre LIKE :search OR m.destination LIKE :search OR m.date LIKE :search OR m.IdContrat LIKE :search)')
               ->setParameter('search', $searchTerm);
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
    $allowedFields = ['idMission', 'titre', 'destination', 'date']; // les champs que tu autorises

    $sortBy = $criteria['sort_by'] ?? null;
    $sortDir = strtolower($criteria['sort_dir'] ?? 'asc');

    if (in_array($sortBy, $allowedFields)) {
        $sortDir = $sortDir === 'desc' ? 'DESC' : 'ASC';
        $qb->orderBy('m.' . $sortBy, $sortDir);
    } else {
        // Tri par défaut
        $qb->orderBy('m.idMission', 'DESC');
    }
}


public function findByTitle(string $title): array
{
    return $this->createQueryBuilder('m')
        ->andWhere('m.titre LIKE :title')
        ->setParameter('title', '%'.$title.'%')
        ->orderBy('m.titre', 'ASC')
        ->getQuery()
        ->getResult();
}

    
  
}






    //    /**
    //     * @return Mission[] Returns an array of Mission objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Mission
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

