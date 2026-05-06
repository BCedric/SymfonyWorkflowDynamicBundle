<?php

namespace BCedric\SymfonyWorkflowDynamicBundle\Controller;

use BCedric\SymfonyWorkflowDynamicBundle\Entity\WorkflowTransition;
use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowPlaceRepository;
use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowTransitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route(path: '/workflow/transition', name: 'wf_transition_')]
class WorkflowTransitionApiController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly WorkflowTransitionRepository $workflowTransitionRepository,
    ) {}

    #[Route(path: '/{target}', name: 'get', methods: ['GET'])]
    public function get(
        string $target
    ) {
        $transitions = $this->workflowTransitionRepository->findByTarget($target);

        return new JsonResponse($this->normalizer->normalize($transitions));
    }

    #[Route(path: '/{target}', name: 'post', methods: ['POST'])]
    #[Route(path: '/{target}/{id}', name: 'put', methods: ['PUT'])]
    public function post(
        Request $request,
        EntityManagerInterface $em,
        WorkflowPlaceRepository $workflowPlaceRepository,
        WorkflowTransitionRepository $workflowTransitionRepository,
        string $target,
        string $id
    ) {
        $body = json_decode($request->getContent(), true);

        if ($id == null) {
            $transition = new WorkflowTransition();
        } else {
            $transition = $workflowTransitionRepository->find($id);
        }

        if (!empty($this->workflowTransitionRepository->findBy(['name' => $body['name'], 'target' => $target]))) {
            return new Response("This transition already exists", 500);
        } else {
            $transition->setName($body['name']);
            $transition->setTarget($target);

            $fromPlaces = [];
            foreach ($body['from'] as $from) {
                $fromPlace = null;
                $fromPlace = $workflowPlaceRepository->findOneBy(['target' => $target, 'name' => $from]);

                if ($fromPlace == null) {
                    return new Response("From places " . $from . " doesn't exist", 500);
                }
                $fromPlaces[] = $fromPlace;
            }
            $transition->setFromPlaces($fromPlaces);

            $toPlaces = [];
            foreach ($body['to'] as $to) {
                $toPlace = $workflowPlaceRepository->findOneBy(['target' => $target, 'name' => $to]);
                if ($toPlace == null) {
                    return new Response("From places " . $to . " doesn't exist", 500);
                }
                $toPlaces[] = $toPlace;
            }
            $transition->setToPlaces($toPlaces);
        }

        $em->persist($transition);

        $em->flush();

        return $this->get($target);
    }

    #[Route(path: '/{target}/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        string $target,
        WorkflowTransition $workflowTransition,
        EntityManagerInterface $em
    ) {
        if ($workflowTransition->getTarget() === $target) {
            $em->remove($workflowTransition);
            $em->flush();
        } else {
            return new Response("Bad target provided", 500);
        }

        return $this->get($target);
    }
}
