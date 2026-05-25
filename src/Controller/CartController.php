<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\OrderService;
use App\Form\CheckoutType;
use App\DTO\CheckoutData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index')]
    public function index(CartService $cartService, ProductRepository $productRepository): Response
    {
        $cart = $cartService->getCart();
        $items = [];
        $total = 0;
        
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if (!$product) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'sum' => $product->getPrice() * $quantity,
            ];

            $total += $product->getPrice() * $quantity;
        }

        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    //#[Route('/add/{id}', name: 'cart_add')]
    //public function add(int $id, CartService $cartService): Response
    //{
    //    $cartService->add($id);

     //   $this->addFlash('success', 'Product added to cart');

    //    return $this->redirectToRoute('cart_index');
    //}

    //#[Route('/remove/{id}', name: 'cart_remove')]
    //public function remove(int $id, CartService $cartService): Response
    //{
    //    $cartService->remove($id);

    //    return $this->redirectToRoute('cart_index');
    //}

    #[Route('/decrease/{id}', name: 'cart_decrease')]
    public function decrease(int $id, CartService $cartService): Response
    {
        $cartService->decrease($id);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/checkout', name: 'cart_checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkout(
        Request $request,
        CartService $cartService,
        OrderService $orderService,
        Security $security
    ): Response {
        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $security->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException();
            }

            $order = $orderService->createOrder(
                $cartService->getCart(),
                $user
            );

            $this->addFlash(
                'success',
                'Ваше закамовлення успішно створено!'
            );

            $cartService->clear();

            if ($this->getUser()) {

                return $this->redirectToRoute('account_index');

            }

            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('cart/checkout.html.twig', [
            'form' => $form->createView(),
            'items' => $cartService->getDetailedCart(),
            'total' => $cartService->getTotalPrice(),
        ]);
    }

    #[Route('/checkout/success', name: 'checkout_success')]
    public function success(): Response
    {
        return new Response('Заказ успешно оформлен!');
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(int $id, CartService $cartService): JsonResponse
    {
        $cartService->add($id);

        return new JsonResponse([
            'success' => true,
            'count' => $cartService->getTotalQuantity(),
        ]);
    }

    #[Route('/mini', name: 'cart_mini', methods: ['GET'])]
    public function mini(CartService $cartService): JsonResponse
    {
        return $this->json([
            'items' => $cartService->getDetailedCart(),
            'total' => $cartService->getTotalPrice(),
            'count' => $cartService->getTotalQuantity(),
        ]);
    }

    #[Route('/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(
        int $id,
        Request $request,
        CartService $cartService
    ): JsonResponse {

        $quantity = (int)$request->request->get('quantity');

        $cartService->updateQuantity($id, $quantity);

        return $this->json([
            'success' => true,
            'cartTotal' => $cartService->getTotalPrice(),
            'cartCount' => $cartService->getTotalQuantity(),
        ]);
    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(
        int $id,
        CartService $cartService
    ): JsonResponse {

        $cartService->remove($id);

        return $this->json([
            'success' => true,
            'cartTotal' => $cartService->getTotalPrice(),
            'cartCount' => $cartService->getTotalQuantity(),
        ]);
    }
}
