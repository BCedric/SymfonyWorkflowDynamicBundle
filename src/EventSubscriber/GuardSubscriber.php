<?php

namespace BCedric\SymfonyWorkflowDynamicBundle\EventSubscriber;

use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowTransitionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class GuardSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly WorkflowTransitionRepository $workflowTransitionRepository
    ) {}
    public function checkGuard(GuardEvent $event)
    {
        // $subject = $event->getSubject();
        // $wfTransition = $this->workflowTransitionRepository->findOneBy(['target' =>  $subject::class, 'name' => $event->getTransition()->getName()]);
        // $guard = $wfTransition->getGuard();
        // if ($guard != null) {
        //     dd($event);
        //     $blocker =  TransitionBlocker::createBlockedByExpressionGuardListener($guard);
        //     dd($blocker);
        //     $event->addTransitionBlocker($blocker);
        // }
    }

    public static function getSubscribedEvents(): array
    {
        return ['workflow.guard' => 'checkGuard'];
    }
}
