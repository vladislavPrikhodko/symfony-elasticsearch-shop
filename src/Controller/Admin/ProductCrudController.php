<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),

            AssociationField::new('categories')
                ->setFormTypeOption('by_reference', false)
                ->setLabel('Категории'),

            TextField::new('name'),
            TextEditorField::new('description'),

            IntegerField::new('price'),
            IntegerField::new('stock'),

            BooleanField::new('isActive'),

            ImageField::new('image')
                ->setBasePath('uploads/products')
                ->setUploadDir('public/uploads/products')
                ->setRequired(false),
            
        ];
    }
}
