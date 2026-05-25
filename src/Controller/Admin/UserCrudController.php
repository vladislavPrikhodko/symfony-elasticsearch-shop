<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email'),
            ArrayField::new('roles'),
            BooleanField::new('isVerified'),
        ];
    }

    public function persistEntity($entityManager, $entityInstance): void
    {
        $this->protectAdminRole($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity($entityManager, $entityInstance): void
    {
        $this->protectAdminRole($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function protectAdminRole($user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $currentUser = $this->getUser();

        if (
            $currentUser instanceof User &&
            $currentUser === $user &&
            !in_array('ROLE_ADMIN', $user->getRoles(), true)
        ) {
            throw new AccessDeniedException(
                'Вы не можете убрать ROLE_ADMIN у самого себя.'
            );
        }
    }
}
