<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\StatutEnum;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "L'identifiant de l'utilisateur ne peut pas être vide.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "L'identifiant doit comporter au moins {{ limit }} caractères.", maxMessage: "L'identifiant ne peut pas dépasser {{ limit }} caractères.")]
    private string $utilisateurId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le sujet ne peut pas être vide.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le sujet doit comporter au moins {{ limit }} caractères.", maxMessage: "Le sujet ne peut pas dépasser {{ limit }} caractères.")]
    private string $sujet;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(min: 10, minMessage: "La description doit comporter au moins {{ limit }} caractères.")]
    private string $description;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    #[Assert\Choice(choices: ['en cours', 'terminé', 'résolu'], message: "Le statut doit être l'un des suivants: 'en cours', 'terminé', ou 'résolu'.")]
    private string $statut;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotBlank(message: "La date de création ne peut pas être vide.")]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reponse = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[Assert\NotBlank(message: "La consultation ne peut pas être vide.")]
    private ?Consultation $consultation = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable(); // Automatically set the creation date
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateurId(): string
    {
        return $this->utilisateurId;
    }

    public function setUtilisateurId(string $utilisateurId): self
    {
        $this->utilisateurId = $utilisateurId;
        return $this;
    }

    public function getSujet(): string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;
        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }
}
