<?php
namespace App\Entity;

use App\Repository\RdvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RdvRepository::class)]
class Rdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Validation for the dateRdv field
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date du rendez-vous ne peut pas être vide.")]
    #[Assert\GreaterThan('today', message: "La date du rendez-vous doit être dans le futur.")]
    private ?\DateTimeInterface $dateRdv = null;

    #[ORM\ManyToOne(inversedBy: 'rdvs')]
    private ?ServiceMed $serviceName = null;

    #[ORM\ManyToOne(inversedBy: 'rdvs')]
    private ?Disponibilite $dispo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->dateRdv;
    }

    public function setDateRdv(\DateTimeInterface $dateRdv): static
    {
        $this->dateRdv = $dateRdv;

        return $this;
    }

    public function getServiceName(): ?ServiceMed
    {
        return $this->serviceName;
    }

    public function setServiceName(?ServiceMed $serviceName): static
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getDispo(): ?Disponibilite
    {
        return $this->dispo;
    }

    public function setDispo(?Disponibilite $dispo): static
    {
        $this->dispo = $dispo;

        return $this;
    }
}