<?php

namespace App\Repository;

use App\Entity\PaidType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaidType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaidType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaidType[]    findAll()
 * @method PaidType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaidTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaidType::class);
    }

    // /**
    //  * @return PaidType[] Returns an array of PaidType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaidType
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
