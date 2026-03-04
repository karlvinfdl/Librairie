<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Vente;
use App\Entity\VenteProduit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // ===== USERS =====
        $user1 = new User();
        $user1->setEmail('karl@example.com');
        $user1->setNom('Karl');
        $user1->setPrenom('Victor');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword(
            $this->passwordHasher->hashPassword($user1, 'password')
        );
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('alice@example.com');
        $user2->setNom('Alice');
        $user2->setPrenom('Doe');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'password')
        );
        $manager->persist($user2);


        // ===== PRODUITS (LIVRES) =====
        $produit1 = (new Produit())
            ->setTitre('Dune')
            ->setPrix('19.99');

        $produit2 = (new Produit())
            ->setTitre('1984')
            ->setPrix('12.50');

        $produit3 = (new Produit())
            ->setTitre('Le Petit Prince')
            ->setPrix('8.90');

        $manager->persist($produit1);
        $manager->persist($produit2);
        $manager->persist($produit3);


        // ===== VENTES =====
        $vente1 = (new Vente())
            ->setUser($user1)
            ->setDateVente(new \DateTimeImmutable());

        $manager->persist($vente1);

        $vente2 = (new Vente())
            ->setUser($user2)
            ->setDateVente(new \DateTimeImmutable('-1 day'));

        $manager->persist($vente2);


        // ===== LIGNES DE VENTE (VENTE_PRODUIT) =====
        $vp1 = (new VenteProduit())
            ->setVente($vente1)
            ->setProduit($produit1)
            ->setQuantite(2);

        $vp2 = (new VenteProduit())
            ->setVente($vente1)
            ->setProduit($produit3)
            ->setQuantite(1);

        $vp3 = (new VenteProduit())
            ->setVente($vente2)
            ->setProduit($produit2)
            ->setQuantite(3);

        $manager->persist($vp1);
        $manager->persist($vp2);
        $manager->persist($vp3);


        // ===== FLUSH =====
        $manager->flush();
    }
}