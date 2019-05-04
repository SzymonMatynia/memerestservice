<?php

namespace App\Repository;

use App\Entity\MemeApi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MemeApi|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemeApi|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemeApi[]    findAll()
 * @method MemeApi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemeApiRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MemeApi::class);
    }
    // /**
    //  * @return MemeApi[] Returns an array of MemeApi objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MemeApi
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
