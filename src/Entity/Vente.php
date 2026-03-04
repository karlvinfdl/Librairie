<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateVente = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, VenteProduit>
     */
    #[ORM\OneToMany(
        targetEntity: VenteProduit::class,
        mappedBy: 'vente',
        orphanRemoval: true
    )]
    private Collection $venteProduits;

    public function __construct()
    {
        $this->venteProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVente(): ?\DateTimeImmutable
    {
        return $this->dateVente;
    }

    public function setDateVente(\DateTimeImmutable $dateVente): static
    {
        $this->dateVente = $dateVente;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, VenteProduit>
     */
    public function getVenteProduits(): Collection
    {
        return $this->venteProduits;
    }

    public function addVenteProduit(VenteProduit $venteProduit): static
    {
        if (!$this->venteProduits->contains($venteProduit)) {
            $this->venteProduits->add($venteProduit);
            $venteProduit->setVente($this);
        }

        return $this;
    }

    public function removeVenteProduit(VenteProduit $venteProduit): static
    {
        if ($this->venteProduits->removeElement($venteProduit)) {
            if ($venteProduit->getVente() === $this) {
                $venteProduit->setVente(null);
            }
        }

        return $this;
    }
}