<?php

// src/EventListener/RequestListener.php
namespace BCedricSymfonyWorkflowDynamicBundle\EventListener;

use BCedricSymfonyWorkflowDynamicBundle\Entity\WorkflowEntity;
use BCedricSymfonyWorkflowDynamicBundle\Service\DynamicWorkflowLoader;
use BCedricSymfonyWorkflowDynamicBundle\Service\DynamicWorkflowServiceFactory;
use Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListener
{

    public function __construct(
        private readonly DynamicWorkflowServiceFactory $dynamicWorkflowServiceFactory,
        private readonly DynamicWorkflowLoader $dynamicWorkflowLoader
    ) {}

    #[AsEventListener()]
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->dynamicWorkflowLoader->loadWorkflows();
    }

    #[AsEventListener()]
    public function checkTargetIsWorkflowable(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (str_starts_with($request->getPathInfo(), '/workflow/')) {

            $target = $request->attributes->get('target');
            if ($target == null) {
                $body = json_decode($request->getContent(), true);
                if (array_key_exists('target', $body)) {
                    $target = json_decode($request->getContent(), true)['target'];
                }
            }

            if ($target != null) {
                $targetInstance = new $target();
                if (!$targetInstance instanceof WorkflowEntity) {
                    throw new Exception("The target entity is not implementing WorkflowEntity class");
                }
            }
        }
    }
}
