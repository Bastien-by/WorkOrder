<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $participant_name = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $technical_position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipantName(): ?string
    {
        return $this->participant_name;
    }

    public function setParticipantName(string $participant_name): static
    {
        $this->participant_name = $participant_name;

        return $this;
    }

    public function getTechnicalPosition(): ?string
    {
        return $this->technical_position;
    }

    public function setTechnicalPosition(?string $technical_position): static
    {
        $this->technical_position = $technical_position;

        return $this;
    }
}
