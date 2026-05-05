<?php

namespace BCedricSymfonyWorkflowDynamicBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\Registry;

#[Route(path: '/workflow', name: 'wf_')]
class WorkflowApiController extends AbstractController
{
    public function __construct(
        private readonly Registry $registry
    ) {}

    #[Route(path: '', name: 'apply_transition', methods: ['PUT'])]
    public function transition(
        Request $request,
        EntityManagerInterface $em,
    ) {
        $body = json_decode($request->getContent(), true);
        $repository = $em->getRepository($body['type']);
        $entity = $repository->find($body['id']);

        /** @var Registry $registry */
        $registry = $this->registry;
        try {
            $wf = $registry->get($entity);
            if ($wf->can($entity, $body['transition'])) {
                $wf->apply($entity, $body['transition']);
                $em->flush();
            } else {
                throw new Exception("Impossible to apply transition");
            }
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), 500);
        }

        return new Response("OK");
    }
}
