<?php

namespace App\Service;

class CartCounterService
{
    public function __construct(private CartService $cartService) {}

    public function __invoke(): int
    {
        return $this->cartService->getTotalQuantity();
    }
}