<?php

namespace App\Entity;

use App\Repository\WorkOrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderRepository::class)]
class WorkOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $technician_name = null;

    #[ORM\Column(length: 30)]
    private ?string $maitenance_type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $intervention_date = null;

    #[ORM\Column(length: 30)]
    private ?string $machine_name = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $technical_position = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $downtime_start_time = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $downtime_end_time = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $intervention_start_time = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $intervention_end_time = null;

    #[ORM\Column(length: 30)]
    private ?string $field_intervention = null;

    #[ORM\Column(length: 30)]
    private ?string $intervention_resquester = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $intervention_request_date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $technical_details = null;

    #[ORM\Column]
    private ?bool $piece_issued = null;

    #[ORM\Column(length: 30)]
    private ?string $piece_type = null;

    #[ORM\Column(length: 30)]
    private ?string $piece_brand = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $sap_reference = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column(length: 50)]
    private ?string $size = null;

    #[ORM\Column(length: 30)]
    private ?string $manufacturer_reference = null;

    #[ORM\Column]
    private ?int $created_piece_quantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $additional_details = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTechnicianName(): ?string
    {
        return $this->technician_name;
    }

    public function setTechnicianName(string $technician_name): static
    {
        $this->technician_name = $technician_name;

        return $this;
    }

    public function getMaitenanceType(): ?string
    {
        return $this->maitenance_type;
    }

    public function setMaitenanceType(string $maitenance_type): static
    {
        $this->maitenance_type = $maitenance_type;

        return $this;
    }

    public function getInterventionDate(): ?\DateTimeImmutable
    {
        return $this->intervention_date;
    }

    public function setInterventionDate(\DateTimeImmutable $intervention_date): static
    {
        $this->intervention_date = $intervention_date;

        return $this;
    }

    public function getMachineName(): ?string
    {
        return $this->machine_name;
    }

    public function setMachineName(string $machine_name): static
    {
        $this->machine_name = $machine_name;

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

    public function getDowntimeStartTime(): ?\DateTimeImmutable
    {
        return $this->downtime_start_time;
    }

    public function setDowntimeStartTime(\DateTimeImmutable $downtime_start_time): static
    {
        $this->downtime_start_time = $downtime_start_time;

        return $this;
    }

    public function getDowntimeEndTime(): ?\DateTimeImmutable
    {
        return $this->downtime_end_time;
    }

    public function setDowntimeEndTime(\DateTimeImmutable $downtime_end_time): static
    {
        $this->downtime_end_time = $downtime_end_time;

        return $this;
    }

    public function getInterventionStartTime(): ?\DateTimeImmutable
    {
        return $this->intervention_start_time;
    }

    public function setInterventionStartTime(\DateTimeImmutable $intervention_start_time): static
    {
        $this->intervention_start_time = $intervention_start_time;

        return $this;
    }

    public function getInterventionEndTime(): ?\DateTimeImmutable
    {
        return $this->intervention_end_time;
    }

    public function setInterventionEndTime(\DateTimeImmutable $intervention_end_time): static
    {
        $this->intervention_end_time = $intervention_end_time;

        return $this;
    }

    public function getFieldIntervention(): ?string
    {
        return $this->field_intervention;
    }

    public function setFieldIntervention(string $field_intervention): static
    {
        $this->field_intervention = $field_intervention;

        return $this;
    }

    public function getInterventionResquester(): ?string
    {
        return $this->intervention_resquester;
    }

    public function setInterventionResquester(string $intervention_resquester): static
    {
        $this->intervention_resquester = $intervention_resquester;

        return $this;
    }

    public function getInterventionRequestDate(): ?\DateTimeImmutable
    {
        return $this->intervention_request_date;
    }

    public function setInterventionRequestDate(\DateTimeImmutable $intervention_request_date): static
    {
        $this->intervention_request_date = $intervention_request_date;

        return $this;
    }

    public function getTechnicalDetails(): ?string
    {
        return $this->technical_details;
    }

    public function setTechnicalDetails(?string $technical_details): static
    {
        $this->technical_details = $technical_details;

        return $this;
    }

    public function isPieceIssued(): ?bool
    {
        return $this->piece_issued;
    }

    public function setPieceIssued(bool $piece_issued): static
    {
        $this->piece_issued = $piece_issued;

        return $this;
    }

    public function getPieceType(): ?string
    {
        return $this->piece_type;
    }

    public function setPieceType(string $piece_type): static
    {
        $this->piece_type = $piece_type;

        return $this;
    }

    public function getPieceBrand(): ?string
    {
        return $this->piece_brand;
    }

    public function setPieceBrand(string $piece_brand): static
    {
        $this->piece_brand = $piece_brand;

        return $this;
    }

    public function getSapReference(): ?string
    {
        return $this->sap_reference;
    }

    public function setSapReference(?string $sap_reference): static
    {
        $this->sap_reference = $sap_reference;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getManufacturerReference(): ?string
    {
        return $this->manufacturer_reference;
    }

    public function setManufacturerReference(string $manufacturer_reference): static
    {
        $this->manufacturer_reference = $manufacturer_reference;

        return $this;
    }

    public function getCreatedPieceQuantity(): ?int
    {
        return $this->created_piece_quantity;
    }

    public function setCreatedPieceQuantity(int $created_piece_quantity): static
    {
        $this->created_piece_quantity = $created_piece_quantity;

        return $this;
    }

    public function getAdditionalDetails(): ?string
    {
        return $this->additional_details;
    }

    public function setAdditionalDetails(?string $additional_details): static
    {
        $this->additional_details = $additional_details;

        return $this;
    }
}
