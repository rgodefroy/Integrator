<?php

namespace Behat\SshExtension;

use Behat\Behat\Extension\ExtensionInterface;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Extension implements ExtensionInterface
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.yml');

        $registryDefinition = $container->getDefinition('ssh.session_registry');
        foreach ($config['connections'] as $connectionName => $connectionConfig) {
            $connectionId = $this->loadConnection($connectionName, $connectionConfig, $container);
            $registryDefinition->addMethodCall('setSession', array($connectionName, new Reference($connectionId)));
        }
    }

    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('connections')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('host')->isRequired()->end()
                            ->scalarNode('port')->defaultValue(22)->end()
                            ->scalarNode('username')->isRequired()->end()
                            ->scalarNode('password')->defaultNull()->end()
                            ->scalarNode('public_key')->defaultNull()->end()
                            ->scalarNode('private_key')->defaultNull()->end()
                            ->scalarNode('passphrase')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function getCompilerPasses()
    {
        return array();
    }

    /**
     * Adds a connection service to the container
     * 
     * @param string $name
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return string The SSH session service id
     */
    private function loadConnection($name, array $config, ContainerBuilder $container)
    {
        $sessionId = sprintf('ssh.session.%s', $name);
        $authId = sprintf('ssh.authentication.%s', $name);
        $confId = sprintf('ssh.configuration.%s', $name);

        // register configuration service
        $confDefinition = new DefinitionDecorator('ssh.configuration');
        $confDefinition->replaceArgument(0, $config['host']);
        $confDefinition->replaceArgument(1, $config['port']);
        $container->setDefinition($confId, $confDefinition);

        // register authentication service
        if ($config['password']) {
            $authDefinition = new DefinitionDecorator('ssh.authentication.password');
            $authDefinition->replaceArgument(1, $config['password']);
        } else {
            $authDefinition = new DefinitionDecorator('ssh.authentication.public_key');
            $authDefinition->replaceArgument(1, $config['public_key']);
            $authDefinition->replaceArgument(2, $config['private_key']);
            $authDefinition->replaceArgument(3, $config['passphrase']);
        }
        $authDefinition->replaceArgument(0, $config['username']);    
        $container->setDefinition($authId, $authDefinition);

        // register session service
        $sessionDefinition = new DefinitionDecorator('ssh.session');
        $sessionDefinition->replaceArgument(0, new Reference($confId));
        $sessionDefinition->replaceArgument(1, new Reference($authId));
        $container->setDefinition($sessionId, $sessionDefinition);

        return $sessionId;
    }
}
