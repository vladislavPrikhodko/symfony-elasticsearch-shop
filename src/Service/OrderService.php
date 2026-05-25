<?php
namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use App\Entity\User;
use App\Entity\OrderStatusHistory;


class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository,
        private TokenStorageInterface $tokenStorage
    ) {}

    public function createOrder(array $cart, User $user): Order
    {
        $order = new Order();
        $order->setUser($user);
        $order->setStatus(OrderStatus::NEW);
        $order->setCreatedAt(new \DateTimeImmutable());

        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if (!$product) {
                continue;
            }

            $item = new OrderItem();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $item->setPrice($product->getPrice());

            $order->addItem($item);

            $total += $product->getPrice() * $quantity;
        }

        $order->setTotal($total);

        $this->em->persist($order);
        $this->em->flush();

        $this->changeStatus($order, OrderStatus::NEW, $user, true);

        return $order;
    }

    public function markAsPaid(Order $order): void
    {
        if ($order->getStatus() !== OrderStatus::NEW) {
            throw new \LogicException('Only new orders can be paid');
        }

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();
        
        $this->changeStatus($order, OrderStatus::PAID, $user);
        
    }

    public function markAsShipped(Order $order): void
    {
        if ($order->getStatus() !== OrderStatus::PAID) {
            throw new \LogicException('Order must be paid before shipping.');
        }

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        $this->changeStatus($order, OrderStatus::SHIPPED, $user);
        
    }

    public function cancel(Order $order, User $user): void
    {
        if ($order->getUser() !== $user) {
            throw new \LogicException('Not your order');
        }

        if ($order->getStatus() !== 'new') {
            throw new \LogicException('Order cannot be canceled');
        }

        
        $this->changeStatus($order, OrderStatus::CANCELED, $user);
        
    }

    public function pay(Order $order): void
    {
        if ($order->getStatus() !== OrderStatus::NEW) {
            throw new \LogicException('Этот заказ нельзя оплатить.');
        }

        foreach ($order->getItems() as $item) {
            $product = $item->getProduct();
            $product->decreaseStock($item->getQuantity());
        }

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        $this->changeStatus($order, OrderStatus::PAID, $user);
        $order->setPaidAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    public function changeStatus(Order $order, string $newStatus, ?User $user = null, $new = false): void
    {
        $oldStatus = $order->getStatus();

        if ($oldStatus === $newStatus && !$new) {
            return;
        }
        if(!$new){
            $order->setStatus($newStatus);
        }

        $history = new OrderStatusHistory();
        $history->setOrderEntity($order);
        $history->setOldStatus($oldStatus);
        $history->setNewStatus($newStatus);
        $history->setChangedBy($user);
        $history->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($history);
        $this->em->flush();
    }
}