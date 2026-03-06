<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    /**
     * @var Collection<int, VenteProduit>
     */
    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: VenteProduit::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $venteProduits;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $imageData = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $imageMime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    public function __construct()
    {
        $this->venteProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    /**
     * @return Collection<int, VenteProduit>
     */
    public function getVenteProduits(): Collection
    {
        return $this->venteProduits;
    }

    /**
     * IMPORTANT :
     * Doctrine retourne souvent un "resource" pour les BLOB.
     * On le transforme en string.
     */
    public function getImageData(): ?string
    {
        if ($this->imageData === null) {
            return null;
        }

        if (is_resource($this->imageData)) {
            $data = stream_get_contents($this->imageData);
            return $data === false ? null : $data;
        }

        return $this->imageData;
    }

    public function setImageData(?string $imageData): static
    {
        $this->imageData = $imageData;
        return $this;
    }

    public function getImageMime(): ?string
    {
        return $this->imageMime;
    }

    public function setImageMime(?string $imageMime): static
    {
        $this->imageMime = $imageMime;
        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;
        return $this;
    }
}