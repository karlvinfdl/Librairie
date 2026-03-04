<?php

namespace App\Controller\Admin;

use App\Entity\Vente;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class VenteCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Vente::class;
  }

  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')->onlyOnIndex(),
      DateTimeField::new('dateVente'),
      AssociationField::new('user'),
      AssociationField::new('venteProduits')->onlyOnIndex(),
    ];
  }
}

