<?php

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

//     /**
//     * @return Site[] Returns an array of Site objects
//      */

    public function findUserAdminInSite($site)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT * FROM `site` inner JOIN user ON site.id = user.no_site_id WHERE user.roles LIKE "%ROLE_ADMIN%" AND site.id=:id;';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $site->getId()]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


    /*
    public function findOneBySomeField($value): ?Site
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
