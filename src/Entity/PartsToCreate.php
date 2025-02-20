<?php

namespace App\Entity;

use App\Repository\PartsToCreateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartsToCreateRepository::class)]
class PartsToCreate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $brand = null;

    #[ORM\Column(length: 30)]
    private ?string $part_type = null;

    #[ORM\Column(length: 255)]
    private ?string $manufacturer_reference = null;

    #[ORM\Column(length: 255)]
    private ?string $dimension = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $additional_description = null;

    #[ORM\Column(length: 255)]
    private ?string $part_to_create_photo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

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

    public function getManufacturerReference(): ?string
    {
        return $this->manufacturer_reference;
    }

    public function setManufacturerReference(string $manufacturer_reference): static
    {
        $this->manufacturer_reference = $manufacturer_reference;

        return $this;
    }

    public function getDimension(): ?string
    {
        return $this->dimension;
    }

    public function setDimension(string $dimension): static
    {
        $this->dimension = $dimension;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAdditionalDescription(): ?string
    {
        return $this->additional_description;
    }

    public function setAdditionalDescription(string $additional_description): static
    {
        $this->additional_description = $additional_description;

        return $this;
    }

    public function getPartToCreatePhoto(): ?string
    {
        return $this->part_to_create_photo;
    }

    public function setPartToCreatePhoto(string $part_to_create_photo): static
    {
        $this->part_to_create_photo = $part_to_create_photo;

        return $this;
    }
}
