<?php

namespace App\Repository;

use App\Entity\ReferenceId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReferenceId|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReferenceId|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReferenceId[]    findAll()
 * @method ReferenceId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReferenceIdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReferenceId::class);
    }

    // /**
    //  * @return ReferenceId[] Returns an array of ReferenceId objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReferenceId
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
