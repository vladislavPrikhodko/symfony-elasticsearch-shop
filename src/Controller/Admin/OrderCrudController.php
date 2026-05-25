<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Controller\Admin\OrderItemCrudController;
use App\Enum\OrderStatus;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {

        $markPaid = Action::new('markPaid', 'Mark as paid')
        ->linkToUrl(function (Order $order) {
            return $this->container
                ->get(AdminUrlGenerator::class)
                ->setController(self::class)
                ->setAction('markPaid')
                ->setEntityId($order->getId())
                ->generateUrl();
        });

        $ship = Action::new('ship', 'Ship order')
        ->linkToUrl(function (Order $order) {
            return $this->container
                ->get(AdminUrlGenerator::class)
                ->setController(self::class)
                ->setAction('ship')
                ->setEntityId($order->getId())
                ->generateUrl();
        });

        

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $markPaid)
            ->add(Crud::PAGE_DETAIL, $ship)
            ->disable(Action::EDIT, Action::DELETE, Action::NEW);
        
    }

    public function configureFields(string $pageName): iterable
    {
        $status = ChoiceField::new('status')
        ->setChoices(OrderStatus::choices())
        ->renderAsBadges([
            'new' => 'warning',
            'paid' => 'success',
            'shipped' => 'info',
        ]);

        //$status = ChoiceField::new('status')->onlyOnIndex();

        if (Crud::PAGE_EDIT === $pageName) {
            return [$status]; // ТОЛЬКО статус
        }
        
        return [
            IdField::new('id')->onlyOnIndex(),
            $status,
            IntegerField::new('total'),
            AssociationField::new('user')
                ->setLabel('Customer'),
            CollectionField::new('items')
                ->onlyOnDetail()
                ->setTemplatePath('admin/order_items.html.twig'),
            CollectionField::new('statusHistory')
                ->onlyOnDetail()
                ->setLabel('История статусов')
                ->setTemplatePath('admin/order_status_history.html.twig'),    
        ];
    }

    public function markPaid(
        Request $request,
        OrderService $orderService,
        EntityManagerInterface $em
    ): RedirectResponse {
        $id = $request->query->get('entityId');

        $order = $em->getRepository(Order::class)->find($id);
        if (!$order) {
            throw $this->createNotFoundException();
        }

        $orderService->markAsPaid($order);

        $this->addFlash('success', 'Order marked as paid');

        return $this->redirect($request->headers->get('referer'));
    }

    public function ship(
        Request $request,
        OrderService $orderService,
        EntityManagerInterface $em
    ): RedirectResponse {
        $id = $request->query->get('entityId');

        $order = $em->getRepository(Order::class)->find($id);
        if (!$order) {
            throw $this->createNotFoundException();
        }

        $orderService->markAsShipped($order);

        $this->addFlash('success', 'Order shipped');

        return $this->redirect($request->headers->get('referer'));
    }

    // public function detail(AdminContext $context)
    // {
    //     $order = $context->getEntity()->getInstance();

    //     dump($order->getItems());
    //     dump(count($order->getItems()));
    //     die;
    // }
}
