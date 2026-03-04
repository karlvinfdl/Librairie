<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        EntityManagerInterface $em
    ): Response {
        // --- AJOUT ---
        if ($request->isMethod('POST') && $request->request->get('action') === 'add') {

            // ✅ Il faut être connecté pour ajouter
            $this->denyAccessUnlessGranted('ROLE_USER');

            $titre = trim((string) $request->request->get('titre'));
            $prix = trim((string) $request->request->get('prix'));

            if ($titre !== '' && $prix !== '') {
                $produit = new Produit();
                $produit->setTitre($titre);
                $produit->setPrix($prix); // DECIMAL => string recommandé

                $em->persist($produit);
                $em->flush();

                $this->addFlash('success', 'Livre ajouté ✅');
                return $this->redirectToRoute('app_home');
            }

            $this->addFlash('error', 'Titre et prix obligatoires.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'produits' => $produitRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    // --- SUPPRESSION ---
    #[Route('/home/produit/{id}/delete', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        ProduitRepository $produitRepository,
        EntityManagerInterface $em
    ): Response {
        // ✅ Seul un admin peut supprimer
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete_produit_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $produit = $produitRepository->find($id);
        if (!$produit) {
            $this->addFlash('error', 'Livre introuvable.');
            return $this->redirectToRoute('app_home');
        }

        $em->remove($produit);
        $em->flush();

        $this->addFlash('success', 'Livre supprimé 🗑️');
        return $this->redirectToRoute('app_home');
    }
}