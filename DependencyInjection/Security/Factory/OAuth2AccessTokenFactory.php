<?php

namespace OAuth2\ClientBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class OAuth2AccessTokenFactory extends AbstractFactory
{
    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'oauth2_access_token';
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
        return 'oauth2.client.security.authentication.access_token_listener';
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = 'security.authentication.listener.oauth2.'.$id;
        $container
            ->setDefinition($listenerId, new DefinitionDecorator('oauth2.client.security.authentication.access_token_listener'));

        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.entry_point.oauth2.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('oauth2.client.security.entry_point.access_token_entry_point'));

        return $entryPointId;
    }
}
