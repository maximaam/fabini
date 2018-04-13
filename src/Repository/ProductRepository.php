<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Category;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param array $categories
     * @return mixed
     */
    public function fetchByCategories(array $categories)
    {
        $qb = $this ->createQueryBuilder('p')
            ->leftJoin('p.category', 'c');

        $qb->where($qb->expr()->in('c.id', $categories));

        return $qb->getQuery()->getResult();
    }
}
