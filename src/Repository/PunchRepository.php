<?php

namespace App\Repository;

use App\Entity\Punch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Punch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Punch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Punch[]    findAll()
 * @method Punch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PunchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Punch::class);
    }

    // /**
    //  * @return Punch[] Returns an array of Punch objects
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
    public function findOneBySomeField($value): ?Punch
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
