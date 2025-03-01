<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipement est obligatoire.")]
    private ?string $nomEquipement = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: "La quantité en stock est obligatoire.")]
    #[Assert\PositiveOrZero(message: "La quantité doit être un nombre positif ou zéro.")]
    private ?int $quantiteStock = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix unitaire est obligatoire.")]
    #[Assert\Positive(message: "Le prix unitaire doit être un nombre positif.")]
    private ?string $prixUnitaire = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'état de l'équipement est obligatoire.")]
    #[Assert\Choice(choices: ["Neuf", "Abimé", "Réparé"], message: "État invalide.")]
    private ?string $etatEquipement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    #[Assert\NotNull(message: "La date d'achat est obligatoire.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "La date d'achat doit être une date valide.")]
    private ?\DateTimeInterface $dateAchat;

    #[ORM\Column(length: 10000)]
    private ?string $img = null;

    public function __construct()
    {
        $this->dateAchat = new \DateTime(); // Définit la date d'achat à aujourd'hui par défaut
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getNomEquipement(): ?string
    {
        return $this->nomEquipement;
    }

    public function setNomEquipement(string $nomEquipement): static
    {
        $this->nomEquipement = $nomEquipement;
        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): static
    {
        $this->quantiteStock = $quantiteStock;
        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getEtatEquipement(): ?string
    {
        return $this->etatEquipement;
    }

    public function setEtatEquipement(string $etatEquipement): static
    {
        $this->etatEquipement = $etatEquipement;
        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): static
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;
        return $this;
    }
}
