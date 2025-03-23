<?php

namespace App\Repository;

use App\Entity\PealimBase;
use App\Service\Unit\Verb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\PealimVocabulary;

/**
 * @extends ServiceEntityRepository<PealimBase>
 */
class PealimBaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PealimBase::class);
    }

    public function loadStandartVocabulary()
    {
        return $this->createQueryBuilder('p')->select('p.*')
            ->leftJoin(PealimVocabulary::class, 'v', 'WITH', 'p.id = v.pealim_vocabulary_id')
            ->setParameter('time', [Verb::INFINITIVE, Verb::TIME_PRESENT])
            ->orderBy('v.time','DESC')
            ->getQuery();
    }
    //    /**
    //     * @return PealimBase[] Returns an array of PealimBase objects
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

    //    public function findOneBySomeField($value): ?PealimBase
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
