<?php

namespace App\Controller\Admin;

use App\Entity\VenteProduit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class VenteProduitCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return VenteProduit::class;
  }

  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')->onlyOnIndex(),
      IntegerField::new('quantite'),
      AssociationField::new('vente'),
      AssociationField::new('produit'),
    ];
  }
}

