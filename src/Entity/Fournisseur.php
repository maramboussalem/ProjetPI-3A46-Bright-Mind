<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomFournisseur = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $Telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    /**
     * @var Collection<int, Medicament>
     */
    #[ORM\OneToMany(targetEntity: Medicament::class, mappedBy: 'fournisseur')]
    private Collection $fournisseur;

    public function __construct()
    {
        $this->fournisseur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFournisseur(): ?string
    {
        return $this->NomFournisseur;
    }

    public function setNomFournisseur(string $NomFournisseur): static
    {
        $this->NomFournisseur = $NomFournisseur;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): static
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->Telephone;
    }

    public function setTelephone(string $Telephone): static
    {
        $this->Telephone = $Telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    /**
     * @return Collection<int, Medicament>
     */
    public function getFournisseur(): Collection
    {
        return $this->fournisseur;
    }

    public function addFournisseur(Medicament $fournisseur): static
    {
        if (!$this->fournisseur->contains($fournisseur)) {
            $this->fournisseur->add($fournisseur);
            $fournisseur->setFournisseur($this);
        }

        return $this;
    }

    public function removeFournisseur(Medicament $fournisseur): static
    {
        if ($this->fournisseur->removeElement($fournisseur)) {
            // set the owning side to null (unless already changed)
            if ($fournisseur->getFournisseur() === $this) {
                $fournisseur->setFournisseur(null);
            }
        }

        return $this;
    }
}
