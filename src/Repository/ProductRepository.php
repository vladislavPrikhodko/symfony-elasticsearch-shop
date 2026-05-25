<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;
use Doctrine\ORM\Query;
use App\Service\ProductSearchService;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ProductSearchService $productSearchService)
    {
        parent::__construct($registry, Product::class);
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategorySlug(string $slug): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }

    public function getCatalogQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery();
    }

    public function getCategoryQuery(Category $category): Query
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $category->getId())
            ->orderBy('p.id', 'DESC')
            ->getQuery();
    }

    public function getFilteredQuery(
        ?string $search = null,
        ?Category $category = null,
        ?string $sort = null
    ): Query
    {
        $qb = $this->createQueryBuilder('p');

        if ($search) {

            //$ids = $searchService->searchIds($query);
            $ids = $this->productSearchService->searchIds($search);            
            if (empty($ids)) {

                $qb->andWhere('1 = 0');

            } else {

                $qb->andWhere('p.id IN (:ids)')
                    ->setParameter('ids', $ids);
            }
        }

        

        if ($category) {

            $qb
                ->join('p.categories', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter(
                    'categoryId',
                    $category->getId()
                );
        }
        
        switch ($sort) {

            case 'price_asc':
                $qb->orderBy('p.price', 'ASC');
                break;

            case 'price_desc':
                $qb->orderBy('p.price', 'DESC');
                break;

            case 'name_asc':
                $qb->orderBy('p.name', 'ASC');
                break;

            default:
                $qb->orderBy('p.id', 'DESC');
        }

        return $qb->getQuery();
    }


}
