<?php

namespace BCedric\SymfonyWorkflowDynamicBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BCedricSymfonyWorkflowDynamicBundle extends AbstractBundle
{

    public function configure(DefinitionConfigurator $definition): void {}

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void {}
}
