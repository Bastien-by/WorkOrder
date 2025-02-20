<?php

namespace App\Entity;

use App\Repository\InterventionDescriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionDescriptionRepository::class)]
class InterventionDescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $long_description = null;

    #[ORM\Column(length: 255)]
    private ?string $part_photo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongDescription(): ?string
    {
        return $this->long_description;
    }

    public function setLongDescription(string $long_description): static
    {
        $this->long_description = $long_description;

        return $this;
    }

    public function getPartPhoto(): ?string
    {
        return $this->part_photo;
    }

    public function setPartPhoto(string $part_photo): static
    {
        $this->part_photo = $part_photo;

        return $this;
    }
}
