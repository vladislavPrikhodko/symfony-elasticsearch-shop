<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

final class ShopController extends AbstractController
{
    #[Route('/', name: 'shop_index')]
    public function index(): Response
    {
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
        ]);
    }

    #[Route('/about', name: 'shop_about')]
    public function about(): Response
    {
        return $this->render('shop/about.html.twig', [
            'controller_name' => 'ShopController',
        ]);
    }

    #[Route('/products', name: 'product_list')]
    public function products(ProductRepository $productRepository): Response
    {
        return $this->render('shop/products.html.twig', [
            'products' => $productRepository->findActive(),
        ]);
    }

    #[Route('/product/{slug}', name: 'product_show')]
    public function show(ProductRepository $productRepo, string $slug, CategoryRepository $categoryRepo): Response
    {
        $product = $productRepo->findOneBy([
            'slug' => $slug,
            'isActive' => true
        ]);

        if (!$product) {
            throw $this->createNotFoundException();
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categoryRepo->findAll(),
        ]);
    }
}
