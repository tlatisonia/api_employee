<?php

namespace App\Repository;

use App\Entity\Emplois;
use App\Entity\Personnes;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Personnes>
 *
 * @method Personnes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnes[]    findAll()
 * @method Personnes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnes::class);
    }

    /**
    * @return Personnes[] Returns an array of Personnes objects
    */
   public function findFields(): array
   {
        return $this->createQueryBuilder('p')
        ->select("p.id","p.nom", "p.prenom","p.dateNaissance")
           ->orderBy('p.nom', 'ASC')
           ->getQuery()
           ->getResult()
       ;
    }

    /**
    * @return Personnes[] Returns an array of Personnes objects
    */
    public function findPersonsforjob($value): array
    {
        return $this->createQueryBuilder('p')
            ->select("p.id","p.nom", "p.prenom","p.dateNaissance","e.nomEntreprise","e.posteOccupe")
            ->join("p.emplois",'e') // assuming 'emplois' is the association field
            ->orderBy('p.nom', 'ASC')
            ->where('e.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Personnes
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


}
