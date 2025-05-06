<?php

namespace App\Repository;

use App\Entity\Recrutement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecrutementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recrutement::class);
    }

    public function findBySearchQuery(string $query, ?string $startDate = null, ?string $endDate = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.offre', 'o')
            ->where('r.dateDebut LIKE :query')
            ->orWhere('r.dateFin LIKE :query')
            ->orWhere('o.titre LIKE :query')
            ->setParameter('query', '%'.$query.'%');

        if ($startDate) {
            $qb->andWhere('r.dateDebut >= :startDate')
                ->setParameter('startDate', new \DateTime($startDate));
        }

        if ($endDate) {
            $qb->andWhere('r.dateFin <= :endDate')
                ->setParameter('endDate', new \DateTime($endDate));
        }

        return $qb->orderBy('r.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Add custom methods as needed
}