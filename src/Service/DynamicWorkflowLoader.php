<?php

namespace BCedricSymfonyWorkflowDynamicBundle\Service;

use BCedricSymfonyWorkflowDynamicBundle\Entity\WorkflowPlace;
use BCedricSymfonyWorkflowDynamicBundle\Entity\WorkflowTransition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Validator\WorkflowValidator;
use Symfony\Component\Workflow\Workflow;

class DynamicWorkflowLoader
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Registry $registry,
        private readonly EventDispatcherInterface $eventDispatcherInterface,
    ) {}

    public function loadWorkflows()
    {
        $targets =  array_map(fn($item) => current($item), $this->entityManager
            ->getRepository(WorkflowPlace::class)->createQueryBuilder('t')
            ->select("t.target")
            ->groupBy('t.target')
            ->getQuery()
            ->getResult());
        foreach ($targets as $target) {
            $this->createWorkflowFromEntities($target);
        }
    }

    private function createWorkflowFromEntities(string $target): Workflow
    {
        $wf_name = 'dynamic_workflow_' . $target;
        $places =
            $this->entityManager
            ->getRepository(WorkflowPlace::class)
            ->findBy(['target' => $target]);

        $transitions =
            $this->entityManager
            ->getRepository(WorkflowTransition::class)
            ->findBy(['target' => $target]);

        // if ($target == 'BCedricSymfonyWorkflowDynamicBundle\Entity\EntityTest2') {
        //     dd($transitions);
        // }

        $definitionBuilder = new DefinitionBuilder();

        foreach ($places as $place) {
            $definitionBuilder->addPlace($place->getName());
        }

        $dispatcher = new EventDispatcher;
        foreach ($transitions as $transition) {
            $transitionObj = new Transition(
                $transition->getName(),
                array_map(fn($place) => $place->getName(), $transition->getFromPlaces()->toArray()),
                array_map(fn($place) => $place->getName(), $transition->getToPlaces()->toArray())
            );

            $definitionBuilder->addTransition($transitionObj, []);
        }



        $definition = $definitionBuilder->build();

        $marking = new MethodMarkingStore();

        $workflow =  new Workflow(
            definition: $definition,
            markingStore: $marking,
            name: $wf_name,
            dispatcher: $this->eventDispatcherInterface,
        );

        $validator = new WorkflowValidator();
        $validator->validate($definition, $wf_name);



        $support = new InstanceOfSupportStrategy($target);
        $this->registry->addWorkflow($workflow, $support);

        return $workflow;
    }

    public function createDynamicWorkflow(string $target): Workflow
    {
        return $this->createWorkflowFromEntities($target);
    }
}
