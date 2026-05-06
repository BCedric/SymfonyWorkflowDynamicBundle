<?php

namespace BCedric\SymfonyWorkflowDynamicBundle\Repository;

use BCedric\SymfonyWorkflowDynamicBundle\Entity\WorkflowEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkflowEntity>
 */
class WorkflowEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkflowEntity::class);
    }

    //    /**
    //     * @return WorkflowEntity[] Returns an array of WorkflowEntity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorkflowEntity
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
