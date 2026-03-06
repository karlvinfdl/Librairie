<?php

namespace App\Controller\User;

use App\Entity\Vente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserDashboardController extends AbstractController
{
  #[Route('/user/dashboard', name: 'app_user_dashboard', methods: ['GET'])]
  public function index(
    TokenStorageInterface $tokenStorage,
    EntityManagerInterface $entityManager
  ): Response {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $user = $this->getUser();

    if (!$user instanceof UserInterface) {
      return $this->redirectToRoute('app_login');
    }

    // Get user's purchases
    $ventes = $entityManager->getRepository(Vente::class)->findBy(
      ['user' => $user],
      ['dateVente' => 'DESC']
    );

    // Calculate total spent
    $totalSpent = 0;
    foreach ($ventes as $vente) {
      foreach ($vente->getVenteProduits() as $vp) {
        $totalSpent += floatval($vp->getProduit()->getPrix()) * $vp->getQuantite();
      }
    }

    return $this->render('user/dashboard.html.twig', [
      'user' => $user,
      'ventes' => $ventes,
      'totalSpent' => number_format($totalSpent, 2, ',', ' '),
    ]);
  }
}

