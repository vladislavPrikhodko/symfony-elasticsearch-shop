<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // public function findPaginatedByUser(User $user, int $page, int $limit = 10): Paginator
    // {
    //     $query = $this->createQueryBuilder('o')
    //         ->andWhere('o.user = :user')
    //         ->setParameter('user', $user)
    //         ->orderBy('o.createdAt', 'DESC')
    //         ->setFirstResult(($page - 1) * $limit)
    //         ->setMaxResults($limit)
    //         ->getQuery();

    //     return new Paginator($query);
    // }

    public function findPaginatedByUser(
        User $user,
        int $page,
        int $limit = 10,
        ?string $status = null
    ): Paginator {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC');

        if ($status) {
            $qb->andWhere('o.status = :status')
            ->setParameter('status', $status);
        }

        $qb->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
