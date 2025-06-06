<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: "Cet email est déjà utilisé.")]
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
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
   // #[Assert\Regex(pattern: "/^[0-9]{8}$/", message: "Le numéro de téléphone doit contenir exactement 8 chiffres.")]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "Les antécédents médicaux doivent comporter au moins {{ limit }} caractères.")]
    private ?string $antecedentsMedicaux = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "La Spécialité doit comporter au moins {{ limit }} caractères.")]
    private ?string $specialite = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 5, minMessage: "Le nom de l'hôpital doit comporter au moins {{ limit }} caractères.")]
    private ?string $hopital = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 8, max: 20, minMessage: "Le mot de passe doit comporter au moins {{ limit }} caractères.", maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères.")]
    #[Assert\Regex(pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/", message: "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.")]
    private ?string $motdepasse = null;

    #[Assert\NotBlank(message: "La confirmation du mot de passe est obligatoire.")]
    #[Assert\EqualTo(propertyPath: "motdepasse", message: "Les mots de passe ne correspondent pas.")]
    private ?string $motdepasse_confirmation = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = false; 
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verificationCode= null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captcha= null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imgUrl = null;
    
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $activationToken = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'user')]
    private Collection $consultations;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
    }

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

    public function getMotdepasseConfirmation(): ?string
    {
        return $this->motdepasse_confirmation;
    }

    public function setMotdepasseConfirmation(string $motdepasse_confirmation): static
    {
        $this->motdepasse_confirmation = $motdepasse_confirmation;

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

    public function setAntecedentsMedicaux(?string $antecedentsMedicaux): static
    {
        $this->antecedentsMedicaux = $antecedentsMedicaux;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getHopital(): ?string
    {
        return $this->hopital;
    }

    public function setHopital(?string $hopital): static
    {
        $this->hopital = $hopital;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->motdepasse;
    }

    public function setPassword(string $password): self
    {
        $this->motdepasse = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // Clear any temporary data (e.g., sensitive fields)
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = 'ROLE_USER'; // Default role if needed
        }
        return ['ROLE_USER', 'ROLE_' . strtoupper($this->role)];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setUser($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            if ($consultation->getUser() === $this) {
                $consultation->setUser(null);
            }
        }

        return $this;
    }


    public function isActive(): bool
    {
       return $this->isActive;
    }

public function setActive(bool $isActive): self
{
    $this->isActive = $isActive;

    return $this;
}

public function getVerificationCode(): ?string
{
    return $this->verificationCode;
}

public function setVerificationCode(?string $verificationCode): self
{
    $this->verificationCode = $verificationCode;

    return $this;
}


public function getCaptcha(): ?string
{
    return $this->captcha;
}

public function setCaptcha(?string $captcha): self
{
    $this->captcha = $captcha;
    return $this;
}

    public function getimgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setimgUrl(?string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;
        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }
}
