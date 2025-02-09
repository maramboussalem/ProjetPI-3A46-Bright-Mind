<?php

// src/Entity/Medicament.php

namespace App\Entity;

use App\Repository\MedicamentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicamentRepository::class)]
class Medicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du médicament ne peut pas être vide.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom doit comporter au moins {{ limit }} caractères.", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $NomMedicament = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La quantité ne peut pas être vide.")]
    #[Assert\Regex(pattern: "/^\d+$/", message: "La quantité doit être un nombre entier.")]
    private ?string $Quantite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")]
    #[Assert\Regex(pattern: "/^\d+(\.\d{1,2})?$/", message: "Le prix doit être un nombre valide avec jusqu'à deux décimales.")]
    private ?string $Prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type ne peut pas être vide.")]
    private ?string $Type = null;

    #[ORM\ManyToOne(inversedBy: 'fournisseur')]
    private ?Fournisseur $fournisseur = null;

    // Getters et setters...

    public function getNomMedicament(): ?string
    {
        return $this->NomMedicament;
    }

    public function setNomMedicament(string $NomMedicament): static
    {
        $this->NomMedicament = $NomMedicament;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;
        return $this;
    }

    public function getQuantite(): ?string
    {
        return $this->Quantite;
    }

    public function setQuantite(string $Quantite): static
    {
        $this->Quantite = $Quantite;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->Prix;
    }

    public function setPrix(string $Prix): static
    {
        $this->Prix = $Prix;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;
        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;
        return $this;
    }
    public function getId(): ?int
{
    return $this->id;
}

}
