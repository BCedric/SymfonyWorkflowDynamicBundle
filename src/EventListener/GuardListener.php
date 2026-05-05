<?php

// src/EventListener/RequestListener.php
namespace BCedric\SymfonyWorkflowDynamicBundle\EventListener;

use BCedric\SymfonyWorkflowDynamicBundle\Repository\WorkflowTransitionRepository;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\EventListener\ExpressionLanguage;
use Symfony\Component\Workflow\TransitionBlocker;

class GuardListener
{

    public function __construct(
        private WorkflowTransitionRepository $workflowTransitionRepository,
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker,
        // private AuthenticationTrustResolverInterface $trustResolver,
        private ?RoleHierarchyInterface $roleHierarchy = null,
        private ?ValidatorInterface $validator = null,
    ) {}

    #[AsGuardListener()]
    public function onTransition(GuardEvent $event, string $eventName): void
    {
        $subject = $event->getSubject();
        $wfTransition = $this->workflowTransitionRepository->findOneBy(['target' =>  $subject::class, 'name' => $event->getTransition()->getName()]);
        $guard = $wfTransition->getGuard();
        if ($guard != null) {
            $this->validateGuardExpression($event, $guard);
        }
    }

    private function validateGuardExpression(GuardEvent $event, string $expression): void
    {
        $expressionLanguage = new ExpressionLanguage();
        if (!$expressionLanguage->evaluate($expression, $this->getVariables($event))) {
            $blocker = TransitionBlocker::createBlockedByExpressionGuardListener($expression);
            $event->addTransitionBlocker($blocker);
            throw new Exception($blocker->getMessage());
        }
    }

    // code should be sync with Symfony\Component\Security\Core\Authorization\Voter\ExpressionVoter
    private function getVariables(GuardEvent $event): array
    {
        $token = $this->tokenStorage->getToken();

        $variables = [
            'subject' => $event->getSubject(),
            // needed for the is_granted expression function
            'auth_checker' => $this->authorizationChecker,
            // needed for the is_* expression function
            // 'trust_resolver' => $this->trustResolver,
            // needed for the is_valid expression function
            'validator' => $this->validator,
        ];

        if (null === $token) {
            return $variables + [
                'token' => null,
                'user' => null,
                'role_names' => [],
            ];
        }

        return $variables + [
            'token' => $token,
            'user' => $token->getUser(),
            'role_names' => $this->roleHierarchy->getReachableRoleNames($token->getRoleNames()),
        ];
    }
}
