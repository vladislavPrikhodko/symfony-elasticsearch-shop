<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Service\ProductSearchService;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(Request $request,
        ProductRepository $productRepo,
        CategoryRepository $categoryRepo,
        PaginatorInterface $paginator
    ): Response
    {
        //$query = $productRepo->createQueryBuilder('p')
        //    ->where('p.isActive = 1')
        //    ->orderBy('p.id', 'DESC');

        // $search = $request->query->get('search');
        $search = $request->query->get('q');

        $sort = $request->query->get('sorting');

        $products = $paginator->paginate(

            $productRepo->getFilteredQuery(
                $search,
                null,
                $sort
            ),

            $request->query->getInt('page', 1),

            8
        );

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepo->findAll(),
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    #[Route('/category/{slug}', name: 'category_show')]
    public function category(
        string $slug,
        ProductRepository $productRepo,
        CategoryRepository $categoryRepo,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $category = $categoryRepo->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $search = $request->query->get('search');

        $sort = $request->query->get('sorting');

        $products = $paginator->paginate(

            $productRepo->getFilteredQuery(
                $search,
                $category,
                $sort
            ),

            $request->query->getInt('page', 1),

            8
        );

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepo->findAll(),
            'currentCategory' => $category,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    
}