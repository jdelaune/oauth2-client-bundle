<?php

namespace OAuth2\ClientBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use OAuth2\ClientBundle\DependencyInjection\Security\Factory\OAuth2Factory;
use OAuth2\ClientBundle\DependencyInjection\Security\Factory\OAuth2AuthCodeFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OAuth2ClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OAuth2Factory());
    }
}
