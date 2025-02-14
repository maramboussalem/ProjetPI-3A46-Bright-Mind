<?php

namespace App\Entity;

use App\Repository\DiagnosticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticRepository::class)]
class Diagnostic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDiagnostic = null;

    #[ORM\Column]
    private ?int $patientID = null;

    #[ORM\Column]
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

    public function setDateDiagnostic(\DateTimeInterface $dateDiagnostic): static
    {
        $this->dateDiagnostic = $dateDiagnostic;

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
