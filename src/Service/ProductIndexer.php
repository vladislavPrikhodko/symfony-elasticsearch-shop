<?php

namespace App\Service;

use App\Entity\Product;

class ProductIndexer
{
    public function __construct(
        private ElasticClient $elastic
    ) {}

    public function index(Product $product): void
    {
        $params = [

            'index' => 'products',

            'id' => $product->getId(),

            'body' => [

                'name' => $product->getName(),

                'description' => $product->getDescription(),

                'price' => $product->getPrice(),

            ]

        ];

        $this->elastic
            ->client()
            ->index($params);
    }
}