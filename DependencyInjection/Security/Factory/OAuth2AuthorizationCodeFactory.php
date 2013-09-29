<?php

namespace OAuth2\ClientBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class OAuth2AuthorizationCodeFactory extends AbstractFactory
{
    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'oauth2_authorization_code';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $node
            ->children()
                ->scalarNode('client_id')->defaultValue('')->end()
                ->scalarNode('client_secret')->defaultValue('')->end()
                ->scalarNode('redirect_uri')->defaultValue('http://www.example.com')->end()
                ->scalarNode('scope')->defaultValue('basic')->end()
            ->end();
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'security.authentication.provider.oauth2.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('oauth2.client.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId));
        ;

        return $providerId;
    }

    protected function getListenerId()
    {
        return 'oauth2.client.security.authentication.authorization_code_listener';
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $container
            ->getDefinition($listenerId)
            ->addMethodCall('setClient', array($config));

        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.entry_point.oauth2.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('oauth2.client.security.entry_point.authorization_code_entry_point'))
            ->replaceArgument(1, $config);

        return $entryPointId;
    }
}
