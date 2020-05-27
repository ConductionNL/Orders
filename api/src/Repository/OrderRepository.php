<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getNextReferenceId($organization, $date = null)
    {
        //if(!$date){
        $start = new \DateTime('first day of January this year');
        $end = new \DateTime('last day of December this year');
        //}

        $result = $this->createQueryBuilder('r')
            ->select('MAX(r.referenceId) AS reference_id')
            ->andWhere(':organization = r.organization')
            ->setParameter('organization', $organization)
            ->andWhere('r.dateCreated >= :start')
            ->setParameter('start', $start)
            ->andWhere('r.dateCreated <= :end')
            ->setParameter('end', $end)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$result) {
            return 1;
        } else {
            return $result['reference_id'] + 1;
        }
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
