<?php

namespace App\Entity;

use App\Repository\ServiceMedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceMedRepository::class)]
class ServiceMed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomService = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionMed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomService(): ?string
    {
        return $this->nomService;
    }

    public function setNomService(string $nomService): static
    {
        $this->nomService = $nomService;

        return $this;
    }

    public function getDescriptionMed(): ?string
    {
        return $this->descriptionMed;
    }

    public function setDescriptionMed(string $descriptionMed): static
    {
        $this->descriptionMed = $descriptionMed;

        return $this;
    }
}
