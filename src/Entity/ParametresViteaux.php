<?php

namespace App\Entity;

use App\Repository\ParametresViteauxRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametresViteauxRepository::class)]
class ParametresViteaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $fc = null;

    #[ORM\Column]
    private ?int $fr = null;

    #[ORM\Column(length: 255)]
    private ?string $ecg = null;

    #[ORM\Column]
    private ?int $TAS = null;

    #[ORM\Column]
    private ?int $TAD = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?int $spo2 = null;

    #[ORM\Column]
    private ?int $gsc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFc(): ?int
    {
        return $this->fc;
    }

    public function setFc(int $fc): static
    {
        $this->fc = $fc;

        return $this;
    }

    public function getFr(): ?int
    {
        return $this->fr;
    }

    public function setFr(int $fr): static
    {
        $this->fr = $fr;

        return $this;
    }

    public function getEcg(): ?string
    {
        return $this->ecg;
    }

    public function setEcg(string $ecg): static
    {
        $this->ecg = $ecg;

        return $this;
    }

    public function getTAS(): ?int
    {
        return $this->TAS;
    }

    public function setTAS(int $TAS): static
    {
        $this->TAS = $TAS;

        return $this;
    }

    public function getTAD(): ?int
    {
        return $this->TAD;
    }

    public function setTAD(int $TAD): static
    {
        $this->TAD = $TAD;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getSpo2(): ?int
    {
        return $this->spo2;
    }

    public function setSpo2(int $spo2): static
    {
        $this->spo2 = $spo2;

        return $this;
    }

    public function getGsc(): ?int
    {
        return $this->gsc;
    }

    public function setGsc(int $gsc): static
    {
        $this->gsc = $gsc;

        return $this;
    }
}
