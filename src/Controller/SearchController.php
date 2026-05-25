<?php

namespace App\Controller;

use App\Service\ProductSearchService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController
{
    #[Route('/search', name: 'search')]
    public function search(
        Request $request,
        ProductSearchService $searchService
    ): Response {

        $query = $request->query->get('q');

        if (!$query) {
            return new Response('Empty query');
        }

        $results = $searchService->search($query);

        return new Response(
            '<pre>' . print_r($results, true) . '</pre>'
        );
    }
}