<?php

namespace App\Entity;

use App\Repository\ServiceMedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ServiceMedRepository::class)]
class ServiceMed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du service ne peut pas être vide')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom du service ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $nomService = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide')]
    #[Assert\Length(
        min: 1,
        minMessage: 'La description doit comporter au moins {{ limit }} caractères'
    )]
    private ?string $descriptionMed = null;

    #[ORM\Column(length: 255, nullable: true)]
    
    #[Assert\Regex(
        pattern: "/\.(jpg|jpeg|png)$/i",
        message: 'L\'image doit être de type JPG, JPEG ou PNG'
    )]
    private ?string $imageM = null;

    /**
     * @var Collection<int, Disponibilite>
     */
    #[ORM\OneToMany(targetEntity: Disponibilite::class, mappedBy: 'ServiceMed')]
    
    private Collection $disponibilites;

    /**
     * @var Collection<int, Rdv>
     */
    #[ORM\OneToMany(targetEntity: Rdv::class, mappedBy: 'serviceName')]
    private Collection $rdvs;

    public function __construct()
    {
        $this->disponibilites = new ArrayCollection();
        $this->rdvs = new ArrayCollection();
    }

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

    public function getImageM(): ?string
    {
        return $this->imageM;
    }

    public function setImageM(?string $imageM): static
    {
        $this->imageM = $imageM;

        return $this;
    }

    /**
     * @return Collection<int, Disponibilite>
     */
    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    public function addDisponibilite(Disponibilite $disponibilite): static
    {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites->add($disponibilite);
            $disponibilite->setServiceMed($this);
        }

        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): static
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getServiceMed() === $this) {
                $disponibilite->setServiceMed(null);
            }
        }

        return $this;
    }

    // Validation personnalisée pour la logique métier
    public function isValid(ExecutionContextInterface $context): bool
    {
        // Vérification de la longueur et de la validité du nom du service
        if (empty($this->nomService) || strlen($this->nomService) > 255) {
            $context->buildViolation('Le nom du service ne peut pas être vide ni dépasser 255 caractères.')
                ->atPath('nomService')
                ->addViolation();
        }


        // Vérification de la validité de l'image
        if ($this->imageM && !preg_match("/\.(jpg|jpeg|png)$/i", $this->imageM)) {
            $context->buildViolation('L\'image doit être de type JPG, JPEG ou PNG.')
                ->atPath('imageM')
                ->addViolation();
        }


        // Retourne true si toutes les validations sont réussies
        return true;
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
            $rdv->setServiceName($this);
        }

        return $this;
    }

    public function removeRdv(Rdv $rdv): static
    {
        if ($this->rdvs->removeElement($rdv)) {
            // set the owning side to null (unless already changed)
            if ($rdv->getServiceName() === $this) {
                $rdv->setServiceName(null);
            }
        }

        return $this;
    }
}

