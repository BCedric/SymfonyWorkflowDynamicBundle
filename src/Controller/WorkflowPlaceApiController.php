<?php

namespace BCedric\SymfonyWorkflowDynamicBundle\Controller;

use BCedric\SymfonyWorkflowDynamicBundle\Entity\WorkflowPlace;
use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowEntityRepository;
use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowPlaceRepository;
use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowTransitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route(path: '/workflow/place', name: 'wf_place_')]
class WorkflowPlaceApiController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer
    ) {}

    #[Route(path: '/{target}', name: 'get', methods: ['GET'])]
    public function get(
        WorkflowPlaceRepository $workflowPlaceRepository,
        string $target
    ) {
        $places = $workflowPlaceRepository->findByTarget($target);

        return new JsonResponse($this->normalizer->normalize($places));
    }

    #[Route(path: '/{target}', name: 'post', methods: ['POST'])]
    #[Route(path: '/{target}/{id}', name: 'put', methods: ['PUT'])]
    public function post(
        WorkflowPlaceRepository $workflowPlaceRepository,
        Request $request,
        EntityManagerInterface $em,
        string $target,
        string $id
    ) {
        $body = json_decode($request->getContent(), true);

        if ($id == null) {
            $place = new WorkflowPlace();
        } else {
            $place = $workflowPlaceRepository->findOneBy(['id' => $id, 'target' => $target]);
        }

        if (!empty($workflowPlaceRepository->findBy(['name' => $body['name'], 'target' => $target]))) {
            return new Response("This place already exists", 500);
        } else {
            $place->setName($body['name']);
            $place->setTarget($target);

            $em->persist($place);
            $em->flush();
        }

        return $this->get($workflowPlaceRepository, $target);
    }

    #[Route(path: '/{target}/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        WorkflowPlaceRepository $workflowPlaceRepository,
        EntityManagerInterface $em,
        string $target,
        WorkflowPlace $workflowPlace,
        WorkflowTransitionRepository $workflowTransitionRepository
    ) {

        try {

            if ($workflowPlace->getTarget() === $target) {
                // $entitiesUsingPlace = $workflowEntityRepository->createQueryBuilder('e')
                //     ->where("e.marking LIKE :value")
                //     ->setParameter('value', "'%\"" . $workflowPlace->getName() . "\"%'")
                //     ->getQuery()
                //     ->getResult();
                // if (!empty($entitiesUsingPlace)) {
                //     throw new Exception("An entity is using this place");
                // }
                $transitionUsingPlace = $workflowTransitionRepository->createQueryBuilder("t")
                    ->where(":value in t.from OR :value in t.to")
                    ->setParameter('value', $workflowPlace)
                    ->getQuery()
                    ->getResult()
                    ;
                if (!empty($transitionUsingPlace)) {
                    throw new Exception("A transition is using this place");
                }
                $em->remove($workflowPlace);
                $em->flush();
            } else {
                throw new Exception("Bad target provided");
            }
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), 500);
            //throw $th;
        }
    }
}
