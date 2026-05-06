# SymfonyWorkflowDynamicBundle

Bundle qui permet de définir des workflows symfony de manière dynamique

## Installation

- Lancer la commande `composer require bcedric/symfony-workflow-dynamic-bundle`
- Ajouter le bundle dans `config/bundle.php` :

```
    <?php

    return [
        // ...
        BCedric\SymfonyWorkflowDynamicBundle\BCedricSymfonyWorkflowDynamicBundle::class => ['all' => true],
    ];

```

- Ajouter dans le fichier `config/packages/doctrine.yaml`:

```
doctrine:
    #...
    orm:
        #...
        mappings:
            #...
            BCedricSymfonyWorkflowDynamicBundle:
                is_bundle: true
                type: attribute
                prefix: 'BCedric\SymfonyWorkflowDynamicBundle\Entity'
                alias: SymfonyWorkflowDynamicBundle
```

- Pour visualiser le workflow dans le profiler, ajouter dans le fichier `config/services.yaml` :

```
dynamic_workflow.entity_test:
       public: true
       class: Symfony\Component\Workflow\WorkflowInterface
       factory: ['@BCedric\SymfonyWorkflowDynamicBundle\Service\DynamicWorkflowServiceFactory', 'create']
       arguments:
          $target: 'App\Entity\EntityTest'
       tags:
          - { name: workflow }
          - { name: workflow.workflow }
          - { name: workflow.dynamic_loader, target: 'App\Entity\EntityTest' }
```

## Entity

- Pour créer une classe sur laquelle appliquer un workflow, celle-ci doit étendre la classe `WorkflowEntity`

```
class EntityTest extends WorkflowEntity
{
    ...
}
```

- Pour créer une instance de cette classe veuillez utiliser le service `WorkflowEntityFactory` :

```
    ...
    $entity = $workflowEntityFactory->create(EntityTest::class);
    ...
```

## API

Au sein de cette api, le paramètre target représente la classe sur laquelle s'applique le workflow.

### WorkflowPlaceApiController

| URL                           | Description                                                                                                  | Méthode | paramètres               |
| ----------------------------- | ------------------------------------------------------------------------------------------------------------ | ------- | ------------------------ |
| /workflow/place/{target}      | Renvoie la liste des étapes du workflow                                                                      | GET     |                          |
| /workflow/place/{target}      | Créer une nouvelle étape sur le workflow                                                                     | POST    | {name: 'nom de l'étape'} |
| /workflow/place/{target}/{id} | Modifie une étape du workflow                                                                                | PUT     | {name: 'nom de l'étape'} |
| /workflow/place/{target}/{id} | Supprime une étape du workflow (/!\ cette fonction peut générer des erreurs si un objet utilise cette étape) | DELETE  |                          |

### WorkflowTransitionApiController

| URL                                | Description                                      | Méthode | paramètres                                                                                                                                                                                                                                                                              |
| ---------------------------------- | ------------------------------------------------ | ------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| /workflow/transition/{target}      | Renvoie la liste des transitions du workflow     | GET     |                                                                                                                                                                                                                                                                                         |
| /workflow/transition/{target}      | Créer une nouvelle transition sur le workflow    | POST    | {name: 'nom de la transition', guard: 'expression de restriction de l'application de la transition utilisant la syntaxe du composant Expression Language de SF', from: [liste des noms des étapes de départ de la transition], to: [liste des noms des étapes de fin de la transition]} |
| /workflow/transition/{target}/{id} | Modifie une nouvelle transition sur le workflow  | POST    | {name: 'nom de la transition', guard: 'expression de restriction de l'application de la transition utilisant la syntaxe du composant Expression Language de SF', from: [liste des noms des étapes de départ de la transition], to: [liste des noms des étapes de fin de la transition]} |
| /workflow/transition/{target}/{id} | Supprime une nouvelle transition sur le workflow | DELETE  | {name: 'nom de la transition', guard: 'expression de restriction de l'application de la transition utilisant la syntaxe du composant Expression Language de SF', from: [liste des noms des étapes de départ de la transition], to: [liste des noms des étapes de fin de la transition]} |

### WorkflowApiController

| URL       | Description             | Méthode | paramètres                                                                                                   |
| --------- | ----------------------- | ------- | ------------------------------------------------------------------------------------------------------------ |
| /workflow | Applique une transition | PUT     | {id: 'id de l'objet à modifier', type: 'classe de l'objet à modifier', transition: 'transition à appliquer'} |
