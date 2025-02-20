<?php

namespace App\Entity;

use App\Repository\DowntimeInterventionDurationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DowntimeInterventionDurationRepository::class)]
class DowntimeInterventionDuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $downtime_start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $intervention_start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $downtime_end_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $intervention_end_time = null;

    #[ORM\Column]
    private ?float $downtime_duration = null;

    #[ORM\Column]
    private ?float $intervention_duration = null;

    #[ORM\Column(length: 30)]
    private ?string $intervention_field = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDowntimeStartTime(): ?\DateTimeInterface
    {
        return $this->downtime_start_time;
    }

    public function setDowntimeStartTime(\DateTimeInterface $downtime_start_time): static
    {
        $this->downtime_start_time = $downtime_start_time;

        return $this;
    }

    public function getInterventionStartTime(): ?\DateTimeInterface
    {
        return $this->intervention_start_time;
    }

    public function setInterventionStartTime(\DateTimeInterface $intervention_start_time): static
    {
        $this->intervention_start_time = $intervention_start_time;

        return $this;
    }

    public function getDowntimeEndTime(): ?\DateTimeInterface
    {
        return $this->downtime_end_time;
    }

    public function setDowntimeEndTime(\DateTimeInterface $downtime_end_time): static
    {
        $this->downtime_end_time = $downtime_end_time;

        return $this;
    }

    public function getInterventionEndTime(): ?\DateTimeInterface
    {
        return $this->intervention_end_time;
    }

    public function setInterventionEndTime(\DateTimeInterface $intervention_end_time): static
    {
        $this->intervention_end_time = $intervention_end_time;

        return $this;
    }

    public function getDowntimeDuration(): ?float
    {
        return $this->downtime_duration;
    }

    public function setDowntimeDuration(float $downtime_duration): static
    {
        $this->downtime_duration = $downtime_duration;

        return $this;
    }

    public function getInterventionDuration(): ?float
    {
        return $this->intervention_duration;
    }

    public function setInterventionDuration(float $intervention_duration): static
    {
        $this->intervention_duration = $intervention_duration;

        return $this;
    }

    public function getInterventionField(): ?string
    {
        return $this->intervention_field;
    }

    public function setInterventionField(string $intervention_field): static
    {
        $this->intervention_field = $intervention_field;

        return $this;
    }
}
