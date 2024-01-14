<?php

namespace App\Repository;

use App\Entity\ExchangeRateMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRateMetadata>
 *
 * @method ExchangeRateMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRateMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRateMetadata[]    findAll()
 * @method ExchangeRateMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRateMetadata::class);
    }

//    /**
//     * @return ExchangeRateMetadata[] Returns an array of ExchangeRateMetadata objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExchangeRateMetadata
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
