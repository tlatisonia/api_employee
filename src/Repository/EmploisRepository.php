<?php

namespace App\Repository;

use App\Entity\Emplois;
use App\Entity\Personnes;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Emplois>
 *
 * @method Emplois|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emplois|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emplois[]    findAll()
 * @method Emplois[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emplois::class);
    }

    /**
     * @return Emplois[] Returns an array of Emplois objects
     */
    public function findBypersonField($value): array
    {
        return $this->createQueryBuilder('e')
        ->select("e.id","e.nomEntreprise", "e.posteOccupe","e.dateDebut")
           ->andWhere('e.id = :val')
            ->setParameter('val', $value)
           ->orderBy('e.id', 'ASC')
          
           ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Emplois
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
