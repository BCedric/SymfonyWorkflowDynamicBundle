<?php

namespace BCedric\SymfonyWorkflowDynamicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass()]
abstract class WorkflowEntity
{
    /**
     * @var array
     */
    #[ORM\Column(length: 255)]
    private array $marking = [];

    public function getMarking(): array
    {
        return $this->marking;
    }

    public function setMarking($marking): WorkflowEntity
    {
        $this->marking = $marking;
        return $this;
    }
}
