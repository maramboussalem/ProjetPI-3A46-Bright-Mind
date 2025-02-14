<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit comporter au moins {{ limit }} caractères.")]
    private ?string $motdepasse = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "Les antécédents médicaux doivent comporter au moins {{ limit }} caractères.")]
    private ?string $antecedentsMedicaux = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "La Specialite doit comporter au moins {{ limit }} caractères.")]
    private ?string $specialite = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "Le nom de l'hôpital doit comporter au moins {{ limit }} caractères.")]
    private ?string $hopital = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "La disponibilité doit comporter au moins {{ limit }} caractères.")]
    private ?string $disponibilite = null;

    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 6, groups: ['registration'])]
    private ?string $motdepasse_confirmation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(string $motdepasse): static
    {
        $this->motdepasse = $motdepasse;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAntecedentsMedicaux(): ?string
    {
        return $this->antecedentsMedicaux;
    }

    public function setAntecedentsMedicaux(string $antecedentsMedicaux): static
    {
        $this->antecedentsMedicaux = $antecedentsMedicaux;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getHopital(): ?string
    {
        return $this->hopital;
    }

    public function setHopital(string $hopital): static
    {
        $this->hopital = $hopital;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(string $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }
    // Implementing the PasswordAuthenticatedUserInterface
    public function setPassword(string $password): self
    {
        $this->motdepasse = $password;
        return $this;
    }
    
    public function getPassword(): ?string
    {
        return $this->motdepasse;
    }


    public function getSalt(): ?string
    {
        // Return null if you don't use a custom salt for password encoding
        return null;
    }

    public function eraseCredentials(): void
    {
        // Clear any temporary data (e.g., sensitive fields)
    }

    public function getMotdepasseConfirmation(): ?string
    {
        return $this->motdepasse_confirmation;
    }

    public function setMotdepasseConfirmation(string $motdepasse_confirmation): static
    {
        $this->motdepasse_confirmation = $motdepasse_confirmation;

        return $this;
    }

    public function getRoles(): array
    {
       return ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
      return $this->email;
    }


}
