<?php

namespace App\Entity;

use App\Repository\ParametresViteauxRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParametresViteauxRepository::class)]
#[ORM\HasLifecycleCallbacks]  // Ajout pour activer PrePersist
class ParametresViteaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être nul.")]
    #[Assert\Length(max: 15, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L'âge est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: "L'âge doit être un nombre positif.")]
    #[Assert\Range(min: 0, max: 120, notInRangeMessage: "L'âge est invalide.")]
    private ?int $age = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La fréquence cardiaque est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: "La fréquence cardiaque doit être positive.")]
    #[Assert\Range(min: 50, max: 180, notInRangeMessage: "La fréquence cardiaque doit être entre {{ min }} et {{ max }}.")]
    private ?int $fc = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La fréquence respiratoire est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: "La fréquence respiratoire doit être positive.")]
    #[Assert\Range(min: 12, max: 30, notInRangeMessage: "La fréquence respiratoire doit être entre {{ min }} et {{ max }}.")]
    private ?int $fr = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'ECG est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "L'ECG ne peut pas dépasser {{ limit }} caractères.")]
    #[Assert\Range(min: 3, max: 15, notInRangeMessage: "L'ECG doit être entre {{ min }} et {{ max }}.")]
    private ?string $ecg = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\NotNull(message: "Le glycémie est obligatoire.")]
    #[Assert\Type("float")]
    #[Assert\Positive(message: "Le glycémie doit être positive.")]
    #[Assert\Range(min: 0, max: 5, notInRangeMessage: "Le glycémie doit être entre {{ min }} et {{ max }}.")]
    private ?float $gad = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La pression artérielle systolique est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: "La pression doit être positive.")]
    #[Assert\Range(min: 80, max: 200, notInRangeMessage: "La pression artérielle systolique doit être entre {{ min }} et {{ max }}.")]
    private ?int $tas = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La pression artérielle diastolique est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: "La pression doit être positive.")]
    #[Assert\Range(min: 80, max: 200, notInRangeMessage: "La pression artérielle diastolique doit être entre {{ min }} et {{ max }}.")]
    private ?int $tad = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le SpO2 est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: "Le SpO2 doit être compris entre {{ min }} et {{ max }}.")]
    private ?int $spo2 = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le GSC est obligatoire.")]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: "Le GSC doit être positif.")]
    private ?int $gsc = null;

    #[ORM\ManyToOne(inversedBy: 'parametresViteauxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    // Constructeur pour initialiser createdAt
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getFc(): ?int { return $this->fc; }
    public function setFc(int $fc): static { $this->fc = $fc; return $this; }
    public function getFr(): ?int { return $this->fr; }
    public function setFr(int $fr): static { $this->fr = $fr; return $this; }
    public function getEcg(): ?string { return $this->ecg; }
    public function setEcg(string $ecg): static { $this->ecg = $ecg; return $this; }
    public function getGad(): ?float { return $this->gad; }
    public function setGad(float $gad): static { $this->gad = $gad; return $this; }
    public function getTas(): ?int { return $this->tas; }
    public function setTas(int $tas): static { $this->tas = $tas; return $this; }
    public function getTad(): ?int { return $this->tad; }
    public function setTad(int $tad): static { $this->tad = $tad; return $this; }
    public function getAge(): ?int { return $this->age; }
    public function setAge(int $age): static { $this->age = $age; return $this; }
    public function getSpo2(): ?int { return $this->spo2; }
    public function setSpo2(int $spo2): static { $this->spo2 = $spo2; return $this; }
    public function getGsc(): ?int { return $this->gsc; }
    public function setGsc(int $gsc): static { $this->gsc = $gsc; return $this; }
    public function getUser(): ?Utilisateur { return $this->user; }
    public function setUser(?Utilisateur $user): static { $this->user = $user; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
