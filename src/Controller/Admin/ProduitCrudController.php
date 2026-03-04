<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('titre'),
            MoneyField::new('prix')->setCurrency('EUR')->setNumDecimals(2),
            TextField::new('imageName')->onlyOnIndex(),
            ImageField::new('imageName', 'Image')
                ->setBasePath('/uploads/images')
                ->onlyOnIndex()
                ->setUploadDir('public/uploads/images')
                ->setRequired(false),
            BooleanField::new('imageData', 'Has Image')->onlyOnIndex(),
        ];
    }
}

