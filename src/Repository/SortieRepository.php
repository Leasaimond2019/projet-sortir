<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;


/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortieByUser($id)
    {
        $test = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.no_organisateur', 'p', Join::WITH, 'p.id =:id')
             ->innerJoin('s.no_etat', 'a', Join::WITH)
             ->where("a.libelle not in ('Archivée')")
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        return $test;
    }
    public function findAllExceptArchivee($id){
        $test=$this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.no_organisateur', 'p', Join::WITH)
            ->innerJoin('s.no_etat', 'a', Join::WITH)
            ->where("a.libelle not in ('Archivée')")
            ->andWhere('((a.libelle in (\'Créée\') and p.id=:id) OR ((a.libelle not in (\'Créée\'))))')
            ->orderBy('s.date_debut', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;

        return $test;
    }
    /*

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */

    public function findBySeveralFields($options)
    {
        $requete = $this->createQueryBuilder("s");
        $requete -> leftJoin("s.no_inscription", "i");

        //foreach [nom colone; valeur ]
        foreach ($options as $option) {
            $requete -> andWhere($option);
        }
        $requete ->orderBy('s.date_debut', 'DESC');
//        dump($requete->getQuery());
//        die();
        return $requete->getQuery()
                        ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Sortie
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
