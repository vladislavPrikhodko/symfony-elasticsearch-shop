<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProductRepository;

class CartService
{
    private const CART_KEY = 'cart';
    private SessionInterface $session;

    public function __construct(private RequestStack $requestStack, private ProductRepository $productRepository) {
        
        // $session = $this->requestStack->getCurrentRequest()?->getSession();

        // if (!$session) {
        //     throw new \RuntimeException('Session is not available.');
        // }

        // $this->session = $session;
    }

    private function getSession()
    {
        $session = $this->requestStack
            ->getCurrentRequest()
            ?->getSession();

        if (!$session) {
            throw new \RuntimeException('Session is not available.');
        }

        return $session;
    }

    public function getCart(): array
    {
        return $this->getSession()->get(self::CART_KEY, []);
    }

    public function add(int $productId): void
    {
        $cart = $this->getCart();

        $cart[$productId] = ($cart[$productId] ?? 0) + 1;

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->getCart();

        unset($cart[$productId]);

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::CART_KEY);
    }

    public function decrease(int $productId): void
    {
        $cart = $this->getCart();

        $quantity = ($cart[$productId] ?? 0);

        if($quantity > 0) $quantity = $quantity - 1;

        $cart[$productId] = $quantity;

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->getCart();

        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item;
        }

        return $total;
    }

    public function getDetailedCart(): array
    {
        $cart = $this->getCart();

        $result = [];

        foreach ($cart as $productId => $item) {
            $product = $this->productRepository->find($productId);

            if (!$product) {
                continue;
            }

            $result[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $item,
                'total' => $product->getPrice() * $item,
            ];
        }

        return $result;
    }

    public function getTotalPrice(): int
    {
        $cart = $this->getCart();

        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = $this->productRepository->find($productId);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $item;
        }

        return $total;
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {

            if (isset($cart[$productId])) {
                $cart[$productId] = $quantity;
            }

        }

        $this->getSession()->set('cart', $cart);
    }
    
}
