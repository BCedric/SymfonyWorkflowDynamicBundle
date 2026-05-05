<?php

namespace BCedricSymfonyWorkflowDynamicBundle\Controller;

use BCedricSymfonyWorkflowDynamicBundle\Entity\WorkflowPlace;
use BCedricSymfonyWorkflowDynamicBundle\Repository\WorkflowPlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}
