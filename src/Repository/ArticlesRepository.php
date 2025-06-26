<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

       /**
        * @return Articles[] Returns an array of Articles objects
        */
       public function findArticlesByLimitPagination(?string $slug = null): array
       {
           $dql =  $this->createQueryBuilder('a')
                ->orderBy('a.id', 'DESC');

           if ($slug) {
                $dql->join('a.category', 'c')
                    ->andwhere('c.slug = :slug')
                    ->setParameter('slug', $slug);
           }

            return $dql->getQuery()->getResult();
       }

    //    public function findOneBySomeField($value): ?Articles
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
