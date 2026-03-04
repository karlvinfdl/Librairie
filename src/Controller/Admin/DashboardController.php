<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ProduitCrudController;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\VenteCrudController;
use App\Controller\Admin\VenteProduitCrudController;
use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Vente;
use App\Entity\VenteProduit;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private $router;
    private $adminUrlGenerator;
    private $entityManager;

    public function __construct(RouterInterface $router, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        // Get statistics
        $produitCount = $this->entityManager->getRepository(Produit::class)->count([]);
        $userCount = $this->entityManager->getRepository(User::class)->count([]);
        $venteCount = $this->entityManager->getRepository(Vente::class)->count([]);
        $venteProduitCount = $this->entityManager->getRepository(VenteProduit::class)->count([]);

        // Calculate total revenue from VenteProduits
        $venteProduits = $this->entityManager->getRepository(VenteProduit::class)->findAll();
        $totalRevenue = 0;
        foreach ($venteProduits as $vp) {
            $totalRevenue += floatval($vp->getProduit()->getPrix()) * $vp->getQuantite();
        }

        return $this->render('admin/dashboard.html.twig', [
            'produitCount' => $produitCount,
            'userCount' => $userCount,
            'venteCount' => $venteCount,
            'venteProduitCount' => $venteProduitCount,
            'totalRevenue' => number_format($totalRevenue, 2, ',', ' '),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Librairie Admin')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Gestion');
        yield MenuItem::linkTo(ProduitCrudController::class, 'Produits', 'fa fa-box');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users');
        yield MenuItem::linkTo(VenteCrudController::class, 'Ventes', 'fa fa-shopping-cart');
        yield MenuItem::linkTo(VenteProduitCrudController::class, 'Vente Produits', 'fa fa-list');
        yield MenuItem::section();
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-arrow-left', '/');
    }
}

