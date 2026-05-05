<?php

namespace BCedricSymfonyWorkflowDynamicBundle\Service;

use BCedricSymfonyWorkflowDynamicBundle\Entity\WorkflowEntity;
use Symfony\Component\Workflow\Registry;

class WorkflowEntityFactory
{

    public function __construct(
        private readonly Registry $registry
    ) {}

    public function create($target)
    {

        /** @var WorkflowEntity */
        $entity = new $target();

        $workflow = $this->registry->get($entity);
        $entity->setMarking(array_fill_keys($workflow->getDefinition()->getInitialPlaces(), 1));

        return $entity;
    }
}
