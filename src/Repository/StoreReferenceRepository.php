<?php

namespace App\Repository;

use App\Entity\StoreReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StoreReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreReference[]    findAll()
 * @method StoreReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreReference::class);
    }

    // /**
    //  * @return StoreReference[] Returns an array of StoreReference objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StoreReference
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
