<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    // #[Route('/account/orders', name: 'account_orders')]
    // public function orders(Request $request, OrderRepository $orderRepository): Response
    // {
    //     $page = max(1, $request->query->getInt('page', 1));
    //     $limit = 5;

    //     $paginator = $orderRepository->findPaginatedByUser(
    //         $this->getUser(),
    //         $page,
    //         $limit
    //     );

    //     $total = count($paginator);
    //     $pages = (int) ceil($total / $limit);

        

    //     return $this->render('account/index.html.twig', [
    //         'orders' => $paginator,
    //         'page' => $page,
    //         'pages' => $pages,
    //     ]);

        
    // }

    #[Route('/account', name: 'account_index')]
    public function index(
        Request $request,
        OrderRepository $orderRepository
    ): Response {
        $page = max(1, $request->query->getInt('page', 1));
        $status = $request->query->get('status');
        $limit = 5;

        $paginator = $orderRepository->findPaginatedByUser(
            $this->getUser(),
            $page,
            $limit,
            $status
        );

        $total = count($paginator);
        $pages = (int) ceil($total / $limit);

        return $this->render('account/index.html.twig', [
            'orders' => $paginator,
            'page' => $page,
            'pages' => $pages,
            'status' => $status,
        ]);
    }

    #[Route('/account/order/{id}', name: 'account_order_show')]
    public function show(
        int $id,
        OrderRepository $orderRepository
    ): Response {
        $order = $orderRepository->find($id);

        if (!$order || $order->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException();
        }

        return $this->render('account/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/account/order/{id}/cancel', name: 'account_order_cancel', methods: ['POST'])]
    public function cancelOrder(
        Order $order,
        Request $request,
        EntityManagerInterface $em,
        OrderService $orderService
    ): Response {
        // 1. Проверка CSRF
        if (!$this->isCsrfTokenValid(
            'cancel-order-' . $order->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException();
        }

        
        $orderService->cancel($order, $this->getUser());
        $em->flush();

        $this->addFlash('success', 'Заказ успешно отменён');

        return $this->redirectToRoute('account_index');
    }


    #[Route('/account/orders/{id}/pay', name: 'account_order_pay', methods: ['POST'])]
    public function payOrder(
        Order $order,
        Request $request,
        OrderService $orderService
    ): Response {
        if (!$this->isCsrfTokenValid('pay'.$order->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        try {
            $orderService->pay($order);
            $this->addFlash('success', 'Заказ успешно оплачен.');
        } catch (\LogicException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('account_index');
    }
}

