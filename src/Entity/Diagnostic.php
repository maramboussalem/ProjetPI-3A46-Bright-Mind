<?php

namespace App\Entity;

use App\Repository\DiagnosticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiagnosticRepository::class)]
class Diagnostic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom est requis.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est requise.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date du diagnostic est requise.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être une date valide.")]
    #[Assert\LessThanOrEqual("today", message: "La date du diagnostic ne peut pas être dans le futur.")]
    private ?\DateTimeInterface $dateDiagnostic = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'identifiant du patient est requis.")]
    #[Assert\Type("integer", message: "L'identifiant du patient doit être un nombre entier.")]
    private ?int $patientID = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'identifiant du médecin est requis.")]
    #[Assert\Type("integer", message: "L'identifiant du médecin doit être un nombre entier.")]
    private ?int $medecinID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getDateDiagnostic(): ?\DateTimeInterface
    {
        return $this->dateDiagnostic;
    }

    public function setDateDiagnostic(?\DateTimeInterface $dateDiagnostic): static
    {
        $this->dateDiagnostic = $dateDiagnostic ?: new \DateTime(); // Default to today if null
        return $this;
    }

    public function getPatientID(): ?int
    {
        return $this->patientID;
    }

    public function setPatientID(int $patientID): static
    {
        $this->patientID = $patientID;

        return $this;
    }

    public function getMedecinID(): ?int
    {
        return $this->medecinID;
    }

    public function setMedecinID(int $medecinID): static
    {
        $this->medecinID = $medecinID;

        return $this;
    }
}
