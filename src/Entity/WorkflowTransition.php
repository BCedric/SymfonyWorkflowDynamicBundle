<?php

namespace BCedricSymfonyWorkflowDynamicBundle\Entity;

use BCedricSymfonyWorkflowDynamicBundle\Repository\WorkflowTransitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: WorkflowTransitionRepository::class)]
class WorkflowTransition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: WorkflowPlace::class)]
    #[JoinTable(name: 'transition_fromPlaces')]
    private $fromPlaces;

    #[ORM\ManyToMany(targetEntity: WorkflowPlace::class)]
    #[JoinTable(name: 'transition_toPlaces')]
    private $toPlaces;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $target = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $guard = null;


    public function __construct()
    {
        $this->fromPlaces = new ArrayCollection();
        $this->toPlaces = new ArrayCollection();
    }

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

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getFromPlaces()
    {
        return $this->fromPlaces;
    }

    public function addFromPlace(WorkflowPlace $fromPlace)
    {
        $this->fromPlaces[] = $fromPlace;

        return $this;
    }

    public function setFromPlaces($fromPlaces)
    {
        $this->fromPlaces = new ArrayCollection($fromPlaces);

        return $this;
    }

    public function removeAllFromPlaces()
    {
        $this->fromPlaces = new ArrayCollection();
        return $this;
    }

    public function removeFromPlaces(WorkflowPlace $fromPlace): self
    {
        $this->fromPlaces->removeElement($fromPlace);

        return $this;
    }

    public function getToPlaces()
    {
        return $this->toPlaces;
    }

    public function addToPlace(WorkflowPlace $toPlace)
    {
        $this->toPlaces[] = $toPlace;

        return $this;
    }

    public function setToPlaces($toPlaces)
    {
        $this->toPlaces = new ArrayCollection($toPlaces);

        return $this;
    }

    public function removeAllToPlaces()
    {
        $this->toPlaces = new ArrayCollection();
        return $this;
    }

    public function removeToPlaces(WorkflowPlace $toPlace): self
    {
        $this->toPlaces->removeElement($toPlace);

        return $this;
    }

    public function getGuard(): ?string
    {
        return $this->guard;
    }

    public function setGuard(?string $guard): static
    {
        $this->guard = $guard;

        return $this;
    }
}
