<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du fournisseur est obligatoire.")]
    private ?string $NomFournisseur = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $Adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^(\+?\d{1,3}[- ]?)?\d{8,14}$/",
        message: "Le numéro de téléphone doit être valide."
    )]
    private ?string $Telephone = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email n'est pas valide.")]
    private ?string $Email = null;

    #[ORM\OneToMany(targetEntity: Medicament::class, mappedBy: 'fournisseur', cascade: ['persist', 'remove'])]
    private Collection $medicaments;

    public function __construct()
    {
        $this->medicaments = new ArrayCollection();
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

    public function getMedicaments(): Collection
    {
        return $this->medicaments;
    }

    public function addMedicament(Medicament $medicament): static
    {
        if (!$this->medicaments->contains($medicament)) {
            $this->medicaments->add($medicament);
            $medicament->setFournisseur($this);
        }
        return $this;
    }

    public function removeMedicament(Medicament $medicament): static
    {
        if ($this->medicaments->removeElement($medicament)) {
            if ($medicament->getFournisseur() === $this) {
                $medicament->setFournisseur(null);
            }
        }
        return $this;
    }

    /**
     * Validation pour s'assurer que NomFournisseur et Adresse sont différents.
     * 
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!empty($this->NomFournisseur) && !empty($this->Adresse) && $this->NomFournisseur === $this->Adresse) {
            $context->buildViolation('Le nom du fournisseur et l\'adresse ne peuvent pas être identiques.')
                ->atPath('Adresse')
                ->addViolation();
        }
    }
}
