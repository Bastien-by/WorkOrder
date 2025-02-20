<?php

namespace App\Entity;

use App\Repository\MaintenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $maintenance_type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $intervention_request_date = null;

    #[ORM\Column(length: 30)]
    private ?string $intervention_requester = null;

    // Relation ManyToMany avec Participant
    #[ORM\ManyToMany(targetEntity: Participant::class)]
    private $participants;

    // Relation ManyToOne avec Machine
    #[ORM\ManyToOne(targetEntity: Machine::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Machine $machine = null;

    // Relation OneToOne avec DowntimeInterventionDuration
    #[ORM\OneToOne(targetEntity: DowntimeInterventionDuration::class, mappedBy: 'maintenance', cascade: ['persist', 'remove'])]
    private ?DowntimeInterventionDuration $downtimeInterventionDuration = null;

    // Relation OneToOne avec InterventionDescription
    #[ORM\OneToOne(targetEntity: InterventionDescription::class, mappedBy: 'maintenance', cascade: ['persist', 'remove'])]
    private ?InterventionDescription $interventionDescription = null;

    // Relation OneToOne avec StoreManagement
    #[ORM\OneToOne(targetEntity: StoreManagement::class, mappedBy: 'maintenance', cascade: ['persist', 'remove'])]
    private ?StoreManagement $storeManagement = null;

    // Relation OneToOne avec PartsToCreate
    #[ORM\OneToOne(targetEntity: PartsToCreate::class, mappedBy: 'maintenance', cascade: ['persist', 'remove'])]
    private ?PartsToCreate $partsToCreate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaintenanceType(): ?string
    {
        return $this->maintenance_type;
    }

    public function setMaintenanceType(string $maintenance_type): static
    {
        $this->maintenance_type = $maintenance_type;

        return $this;
    }

    public function getInterventionRequestDate(): ?\DateTimeInterface
    {
        return $this->intervention_request_date;
    }

    public function setInterventionRequestDate(\DateTimeInterface $intervention_request_date): static
    {
        $this->intervention_request_date = $intervention_request_date;

        return $this;
    }

    public function getInterventionRequester(): ?string
    {
        return $this->intervention_requester;
    }

    public function setInterventionRequester(string $intervention_requester): static
    {
        $this->intervention_requester = $intervention_requester;

        return $this;
    }

    // Getter et Setter pour les relations

    public function getParticipants(): mixed
    {
        return $this->participants;
    }

    public function setParticipants(mixed $participants): static
    {
        $this->participants = $participants;
        return $this;
    }

    public function getMachine(): ?Machine
    {
        return $this->machine;
    }

    public function setMachine(Machine $machine): static
    {
        $this->machine = $machine;

        return $this;
    }

    public function getDowntimeInterventionDuration(): ?DowntimeInterventionDuration
    {
        return $this->downtimeInterventionDuration;
    }

    public function setDowntimeInterventionDuration(DowntimeInterventionDuration $downtimeInterventionDuration): static
    {
        $this->downtimeInterventionDuration = $downtimeInterventionDuration;

        return $this;
    }

    public function getInterventionDescription(): ?InterventionDescription
    {
        return $this->interventionDescription;
    }

    public function setInterventionDescription(InterventionDescription $interventionDescription): static
    {
        $this->interventionDescription = $interventionDescription;

        return $this;
    }

    public function getStoreManagement(): ?StoreManagement
    {
        return $this->storeManagement;
    }

    public function setStoreManagement(StoreManagement $storeManagement): static
    {
        $this->storeManagement = $storeManagement;

        return $this;
    }

    public function getPartsToCreate(): ?PartsToCreate
    {
        return $this->partsToCreate;
    }

    public function setPartsToCreate(PartsToCreate $partsToCreate): static
    {
        $this->partsToCreate = $partsToCreate;

        return $this;
    }
}
