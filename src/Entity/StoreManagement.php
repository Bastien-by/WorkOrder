<?php

namespace App\Entity;

use App\Repository\StoreManagementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreManagementRepository::class)]
class StoreManagement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $part_outflow = null;

    #[ORM\Column(length: 30)]
    private ?string $part_type = null;

    #[ORM\Column(length: 30)]
    private ?string $brand_manufacturer = null;

    #[ORM\Column(length: 30)]
    private ?string $sap_part_reference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isPartOutflow(): ?bool
    {
        return $this->part_outflow;
    }

    public function setPartOutflow(bool $part_outflow): static
    {
        $this->part_outflow = $part_outflow;

        return $this;
    }

    public function getPartType(): ?string
    {
        return $this->part_type;
    }

    public function setPartType(string $part_type): static
    {
        $this->part_type = $part_type;

        return $this;
    }

    public function getBrandManufacturer(): ?string
    {
        return $this->brand_manufacturer;
    }

    public function setBrandManufacturer(string $brand_manufacturer): static
    {
        $this->brand_manufacturer = $brand_manufacturer;

        return $this;
    }

    public function getSapPartReference(): ?string
    {
        return $this->sap_part_reference;
    }

    public function setSapPartReference(string $sap_part_reference): static
    {
        $this->sap_part_reference = $sap_part_reference;

        return $this;
    }
}
