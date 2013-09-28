<?php

namespace OAuth2\ClientBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class OAuth2Factory extends AbstractFactory
{
    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'oauth2';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $node
            ->children()
                ->scalarNode('client_id')->defaultValue('')->end()
                ->scalarNode('client_secret')->defaultValue('')->end()
                ->scalarNode('authorized_redirect_uri')->defaultValue('https://www.example.com/authorized')->end()
                ->scalarNode('scope')->defaultValue('basic')->end()
                ->scalarNode('redirect_uri')->defaultValue('http://www.example.com')->end()
                ->scalarNode('client_id')->defaultValue('')->end()
                ->booleanNode('authorization_code')->defaultTrue()->end()
            ->end();
    }

    protected function getListenerId()
    {
        return 'oauth2.client.security.authentication.listener';
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
        if ($config['authorization_code'] === FALSE) {
            $entryPointService = 'oauth2.client.security.entry_point.access_token_entry_point';
        }
        else {
            $entryPointService = 'oauth2.client.security.entry_point.authorization_code_entry_point';
        }

        $entryPointId = 'security.authentication.entry_point.oauth2.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator($entryPointService))
            ->replaceArgument(1, $config);

        return $entryPointId;
    }
}
