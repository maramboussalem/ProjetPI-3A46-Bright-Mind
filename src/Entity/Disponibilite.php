<?php

namespace App\Entity;

use App\Repository\DisponibiliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DisponibiliteRepository::class)]
class Disponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeure = null;

    #[ORM\Column]
    private ?bool $estReserve = null;
    #[ORM\ManyToOne(targetEntity: ServiceMed::class, inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?ServiceMed $ServiceMed = null;

    /**
     * @var Collection<int, Rdv>
     */
    #[ORM\OneToMany(targetEntity: Rdv::class, mappedBy: 'dispo')]
    private Collection $rdvs;

    public function __construct()
    {
        $this->rdvs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTimeInterface $dateHeure): static
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    public function isEstReserve(): ?bool
    {
        return $this->estReserve;
    }

    public function setEstReserve(bool $estReserve): static
    {
        $this->estReserve = $estReserve;

        return $this;
    }

    public function getServiceMed(): ?ServiceMed
    {
        return $this->ServiceMed;
    }

    public function setServiceMed(?ServiceMed $ServiceMed): static
    {
        $this->ServiceMed = $ServiceMed;

        return $this;
    }

    /**
     * @return Collection<int, Rdv>
     */
    public function getRdvs(): Collection
    {
        return $this->rdvs;
    }

    public function addRdv(Rdv $rdv): static
    {
        if (!$this->rdvs->contains($rdv)) {
            $this->rdvs->add($rdv);
            $rdv->setDispo($this);
        }

        return $this;
    }

    public function removeRdv(Rdv $rdv): static
    {
        if ($this->rdvs->removeElement($rdv)) {
            // set the owning side to null (unless already changed)
            if ($rdv->getDispo() === $this) {
                $rdv->setDispo(null);
            }
        }

        return $this;
    }
}
