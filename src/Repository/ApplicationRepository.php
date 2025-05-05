<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function findOneByUserAndRecrutement(int $userId, int $recrutementId): ?Application
    {
        return $this->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->join('a.recrutement', 'r')
            ->andWhere('u.id = :userId')
            ->andWhere('r.id = :recrutementId')
            ->setParameter('userId', $userId)
            ->setParameter('recrutementId', $recrutementId)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findByRecrutementId(int $recrutementId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.recrutement = :recrutementId')
            ->setParameter('recrutementId', $recrutementId)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Application[] Returns an array of Application objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Application
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
