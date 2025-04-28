<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    // Add custom methods as needed
    public function findByFilters($query, $departementId)
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.departement', 'd')
            ->where('o.titre LIKE :query')
            ->orWhere('o.description LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        if ($departementId) {
            $qb->andWhere('d.id = :departementId')
                ->setParameter('departementId', $departementId);
        }

        return $qb->getQuery()->getResult();
    }
}