<?php

namespace BCedricSymfonyWorkflowDynamicBundle\Service;

use Symfony\Component\Workflow\Workflow;

class DynamicWorkflowServiceFactory
{
    public function create(DynamicWorkflowLoader $loader, string $target): Workflow
    {
        return $loader->createDynamicWorkflow($target);
    }
}
