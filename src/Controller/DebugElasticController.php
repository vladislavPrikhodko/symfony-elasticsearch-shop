<?php

namespace App\Controller;

use App\Service\ElasticClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DebugElasticController
{
    #[Route('/debug/es', name: 'debug_es')]
    public function test(ElasticClient $elastic): Response
    {
        $info = $elastic->client()->info();

        return new Response('<pre>' . print_r($info, true) . '</pre>');
    }
}