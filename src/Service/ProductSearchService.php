<?php

namespace App\Service;

class ProductSearchService
{
    public function __construct(
        private ElasticClient $elastic
    ) {}

    public function search(string $query): array
    {
        $params = [

            'index' => 'products',

            'body' => [

                'query' => [

                    'multi_match' => [

                        'query' => $query,

                        'fields' => [
                            'name^3',
                            'description'
                        ],
                        'fuzziness' => 'AUTO'

                    ]

                ]

            ]

        ];

        $results = $this->elastic
            ->client()
            ->search($params);

        return $results['hits']['hits'];
    }

    public function searchIds(string $query): array
    {
        $params = [

            'index' => 'products',

            'body' => [

                'query' => [

                    'multi_match' => [

                        'query' => $query,

                        'fields' => [
                            'name^3',
                            'description'
                        ],

                        'fuzziness' => 'AUTO'

                    ]

                ]

            ]

        ];

        $results = $this->elastic
            ->client()
            ->search($params);

        $ids = [];

        foreach ($results['hits']['hits'] as $hit) {
            $ids[] = (int)$hit['_id'];
        }

        return $ids;
    }
}