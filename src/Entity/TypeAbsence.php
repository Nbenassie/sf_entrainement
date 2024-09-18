<?php

namespace App\Entity;

use App\Repository\TypeAbsenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeAbsenceRepository::class)]
class TypeAbsence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $CodetypeAbsence = null;

    #[ORM\Column(length: 255)]
    private ?string $Denomination = null;

    #[ORM\Column]
    private ?bool $Active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $absenceColor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodetypeAbsence(): ?string
    {
        return $this->CodetypeAbsence;
    }

    public function setCodetypeAbsence(string $CodetypeAbsence): static
    {
        $this->CodetypeAbsence = $CodetypeAbsence;

        return $this;
    }

    public function getDenomination(): ?string
    {
        return $this->Denomination;
    }

    public function setDenomination(string $Denomination): static
    {
        $this->Denomination = $Denomination;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->Active;
    }

    public function setActive(bool $Active): static
    {
        $this->Active = $Active;

        return $this;
    }

    public function getAbsenceColor(): ?string
    {
        return $this->absenceColor;
    }

    public function setAbsenceColor(?string $absenceColor): static
    {
        $this->absenceColor = $absenceColor;

        return $this;
    }
}
