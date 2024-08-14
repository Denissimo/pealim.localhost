<?php

namespace App\Repository;

use App\Entity\PealimVocabulary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PealimVocabulary>
 */
class PealimVocabularyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PealimVocabulary::class);
    }

    /**
     * @return PealimVocabulary[]|array Returns an array of PealimVocabulary objects
     */
    public function findBySlug($value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.slug = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PealimVocabulary[] Returns an array of PealimVocabulary objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PealimVocabulary
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
