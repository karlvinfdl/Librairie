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

        // =========================
        // AJOUT
        // =========================
        if ($request->isMethod('POST') && $request->request->get('action') === 'add') {

            // CSRF validation
            if (!$this->isCsrfTokenValid('add_produit', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException();
            }

            // USER (admin aussi si hiérarchie)
            $this->denyAccessUnlessGranted('ROLE_USER');

            $titre = trim((string) $request->request->get('titre'));
            $prixRaw = str_replace(',', '.', trim((string) $request->request->get('prix')));

            if ($titre === '' || $prixRaw === '' || !is_numeric($prixRaw)) {
                $this->addFlash('error', 'Titre et prix obligatoires');
                return $this->redirectToRoute('app_home');
            }

            $produit = new Produit();
            $produit->setTitre($titre);
            $produit->setPrix(number_format((float) $prixRaw, 2, '.', ''));

            // ✅ Upload (Windows-friendly)
            $imageFile = $request->files->all()['image'] ?? null;

            if ($imageFile) {
                $mime = (string) $imageFile->getMimeType();

                if (!str_starts_with($mime, 'image/')) {
                    $this->addFlash('error', 'Le fichier doit être une image');
                    return $this->redirectToRoute('app_home');
                }

                // lecture fichier
                $bytes = @file_get_contents($imageFile->getPathname());
                if ($bytes === false || $bytes === '') {
                    $this->addFlash('error', "Impossible de lire l'image uploadée");
                    return $this->redirectToRoute('app_home');
                }

                $produit->setImageMime($mime);
                $produit->setImageName($imageFile->getClientOriginalName());
                $produit->setImageData($bytes);
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté');
            return $this->redirectToRoute('app_home');
        }

        // =========================
        // LISTE
        // =========================
        return $this->render('home/index.html.twig', [
            'produits' => $produitRepository->findBy([], ['id' => 'DESC'])
        ]);
    }


    // =========================
    // AFFICHAGE IMAGE (BLOB)
    // =========================
    #[Route('/produit/{id}/image', name: 'app_produit_image', methods: ['GET'])]
    public function image(int $id, ProduitRepository $repo): Response
    {
        $produit = $repo->find($id);

        if (!$produit) {
            throw $this->createNotFoundException();
        }

        $image = $produit->getImageData();
        if (!$image) {
            throw $this->createNotFoundException();
        }

        // sécurité : si jamais on reçoit un stream
        if (is_resource($image)) {
            $image = stream_get_contents($image);
        }

        if (!is_string($image) || $image === '') {
            throw $this->createNotFoundException();
        }

        $mime = $produit->getImageMime() ?: 'image/jpeg';

        return new Response(
            $image,
            200,
            [
                'Content-Type' => $mime,
                'Content-Length' => (string) strlen($image),
            ]
        );
    }


    // =========================
    // MODIFIER (USER)
    // =========================
    #[Route('/home/produit/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        ProduitRepository $repo,
        EntityManagerInterface $em
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $produit = $repo->find($id);
        if (!$produit) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            $titre = trim((string) $request->request->get('titre'));
            $prixRaw = str_replace(',', '.', trim((string) $request->request->get('prix')));

            if ($titre === '' || $prixRaw === '' || !is_numeric($prixRaw)) {
                $this->addFlash('error', 'Titre et prix obligatoires');
                return $this->redirectToRoute('app_produit_edit', ['id' => $id]);
            }

            $produit->setTitre($titre);
            $produit->setPrix(number_format((float) $prixRaw, 2, '.', ''));

            // upload image optionnel
            $imageFile = $request->files->all()['image'] ?? null;

            if ($imageFile) {
                $mime = (string) $imageFile->getMimeType();

                if (!str_starts_with($mime, 'image/')) {
                    $this->addFlash('error', 'Image invalide');
                    return $this->redirectToRoute('app_produit_edit', ['id' => $id]);
                }

                $bytes = @file_get_contents($imageFile->getPathname());
                if ($bytes === false || $bytes === '') {
                    $this->addFlash('error', "Impossible de lire l'image uploadée");
                    return $this->redirectToRoute('app_produit_edit', ['id' => $id]);
                }

                $produit->setImageMime($mime);
                $produit->setImageName($imageFile->getClientOriginalName());
                $produit->setImageData($bytes);
            }

            $em->flush();

            $this->addFlash('success', 'Livre modifié');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/edit.html.twig', [
            'produit' => $produit
        ]);
    }


    // =========================
    // SUPPRIMER (ADMIN)
    // =========================
    #[Route('/home/produit/{id}/delete', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        ProduitRepository $repo,
        EntityManagerInterface $em
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete_produit_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $produit = $repo->find($id);
        if ($produit) {
            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}