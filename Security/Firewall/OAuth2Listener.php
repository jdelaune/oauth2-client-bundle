<?php

namespace OAuth2\ClientBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use OAuth2\ClientBundle\Security\Authentication\Token\OAuth2AccessToken;

class OAuth2Listener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Get access token
        $authHeader = preg_split('/[\s]+/', $request->headers->get('Authorization'));
        $access_token = isset($authHeader[1]) ? $authHeader[1] : $request->get('access_token');

        if (!empty($access_token)) {
            $token = new OAuth2AccessToken();
            $token->setAccessToken($access_token);

            try {
                $authToken = $this->authenticationManager->authenticate($token);
                $this->securityContext->setToken($authToken);

                return;
            }
            catch (AuthenticationException $failed)
            {
                // Deny authentication with a '403 Forbidden' HTTP response
                $response = new Response(NULL, 403);
                $event->setResponse($response);
            }
        }

        // By default deny authorization
        $response = new Response(NULL, 403);
        $event->setResponse($response);
    }
}