<?php

namespace App\Repository;

use App\Entity\SearchSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SearchSortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchSortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchSortie[]    findAll()
 * @method SearchSortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchSortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchSortie::class);
    }

    // /**
    //  * @return SearchSortie[] Returns an array of SearchSortie objects
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
    public function findOneBySomeField($value): ?SearchSortie
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
